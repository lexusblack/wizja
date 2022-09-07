<?php

namespace common\models;

use common\helpers\ArrayHelper;
use common\models\base\Expense as BaseExpense;
use kartik\mpdf\Pdf;
use Yii;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * This is the model class for table "expense".
 */
class Expense extends BaseExpense
{
    const PAYMENTMETHOD_CASH = 'cash';// - gotówka
    const PAYMENTMETHOD_TRANSFER = 'transfer';// - przelew
    const PAYMENTMETHOD_COMPENSATION = 'compensation';// - kompensata
    const PAYMENTMETHOD_COD = 'cod';// - za pobraniem
    const PAYMENTMETHOD_CARD = 'payment_card';// - kartą płatniczą

    const NO_ITEMS_TYPE = 10000;

    const TYPE_NO_VAT = 0;
    const TYPE_VAT = 1;

    public $eventIds;
    public $needToLink = false;


    public function getTypeButton()
    {
        $type = ExpenseType::findOne($this->expense_type);
        return '<span class="label label-primary" style="background-color:'.$type->color.';"">'.$type->name.'</span>';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'eventIds'
            ],
            'relations' => [
                'events',
            ],
            'modelClasses' => [
                'common\models\Event',
            ],
        ];
        return $behaviors;
    }

    public function rules()
    {
        $rules = [
            [['paid'], 'boolean'],
            [['customer_id', 'number'], 'required'],
            [['eventIds'], 'each', 'rule' => ['integer']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_VAT => Yii::t('app', 'Faktura VAT'),
            self::TYPE_NO_VAT => Yii::t('app', 'Faktura bez VAT'),

        ];
    }

        public function countPayments()
    {
        $sum = 0;
        foreach($this->expensePaymentHistories as $payment)
        {
            $sum +=$payment->amount;
        }
        $this->alreadypaid = $sum;
        $this->remaining = $this->total - $this->alreadypaid;
        if ($this->alreadypaid>=$this->total)
        {
            $this->paid = 1;
        }else{
            $this->paid = 0;
        }
        $this->save();
    }

    public static function getExpenseTypeList()
    {
        /*$list = [
            1=>Yii::t('app', 'Zakup tow. handlowych oraz materiałów podst.'),
            100 => Yii::t('app', 'Inne wydatki związane z działalnością gospodarczą'),
            101 => Yii::t('app', 'Zakup paliwa do pojazdu'),
            102 => Yii::t('app', 'Koszty mediów i usług telekomunikacyjnych'),
        ];*/
        $list = ArrayHelper::map(ExpenseType::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
        return $list;
    }

    public function getExpenseTypeLabel()
    {
        $list = static::getExpenseTypeList();
        $index = $this->expense_type;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }


    public static function getPaymentmethodList()
    {
        return Paymentmethod::getModelList();
    }

    public function getPaymentmethodLabel()
    {
        if ($this->paymentmethod_id==null)
        {
            $this->paymentmethod_id = 1;
        }
        return $this->paymentmethod0->name;
//        $list = static::getPaymentmethodList();
//        $index = $this->paymentmethod;
//        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function attributeLabels()
    {
        $labels = [
            'paid' => Yii::t('app', 'Zapłacono całość'),
            'eventIds'=> Yii::t('app', 'Wydarzenia'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function attributesUpdate()
    {
            if ($this->date == null)
            {
                $this->date = date('Y-m-d');
            }
            $date = \DateTime::createFromFormat('Y-m-d', $this->date);
            $this->day = $date->format('d');
            $this->month = $date->format('m');
            $this->year = $date->format('Y');

        $query = $this->getExpenseContents();
        $query->select([
            'price'=>new Expression('sum(price)'),
            'tax'=>new Expression('sum(tax)'),
            'netto'=>new Expression('sum(netto)'),
            'brutto'=>new Expression('sum(brutto)'),
            'discount'=>new Expression('sum(discount)'),
        ]);

        $summary = $query->asArray()->one();

        $this->tax = $summary['tax'];
        $this->netto = $summary['netto'];
        $this->total = $summary['brutto'];

        $query = $this->getExpenseContentRates();
        $query->select([
            'tax'=>new Expression('sum(tax)'),
            'netto'=>new Expression('sum(netto)'),
            'brutto'=>new Expression('sum(brutto)'),
        ]);

        $summary = $query->asArray()->one();

        $this->tax += $summary['tax'];
        $this->netto += $summary['netto'];
        $this->total += $summary['brutto'];


        if ($this->alreadypaid_initial==null)
        {
            $this->alreadypaid_initial = $this->alreadypaid;
        }
        if ($this->paid==1)
        {
            $this->alreadypaid = $this->total;
        }
        $this->remaining = $this->total - $this->alreadypaid;
    }

    public function loadPdf()
    {
        $data = $this->loadData();
        $model = $data['model'];
        $tmpModel = new Expense();
        $tmpContent = new ExpenseContent();

        $settings = Yii::$app->settings;

        $content = Yii::$app->view->render('@backend/modules/finances/views/expense/_content', ['model'=>$model, 'data'=>$data, 'tmpModel'=>$tmpModel, 'tmpContent'=>$tmpContent,]);
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
//            'marginLeft'=>0,
//            'marginRight'=>0,
            // any css to be embedded if required
            'cssInline' => 'body {font-size:10px}',
            'filename'=>Inflector::slug(Yii::t('app', 'Koszt').' -'.$model['number']).'.pdf',
            // set mPDF properties on the fly
//            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
//            'methods' => [
//                'SetHeader'=>['Krajee Report Header'],
//                'SetFooter'=>['{PAGENO}'],
//            ]
        ]);

        return $pdf;
    }

    public function storeData()
    {
        $this->data = null;
        $settings = Yii::$app->settings;
        $model = $this;
        $data =[];
        $data = [
            'buyer'=> [
                'logo' => $settings->get('companyLogo', 'main'),
                'name' => $settings->get('companyName', 'main'),
                'address' => $settings->get('companyAddress', 'main'),
                'city' => $settings->get('companyCity', 'main'),
                'nip' => $settings->get('companyNIP', 'main'),
                'bankName' => $settings->get('companyBankName', 'main'),
                'bankNumber' => $settings->get('companyBankNumber', 'main'),

            ],

        ];
        if ($this->customer !== null)
        {
            $customer = $model->customer;
            $data['seller'] = [
                'logo' => $customer->logo,
                'name' => $customer->name,
                'address' => $customer->address,
                'nip' => $customer->nip,
                'city'=>$customer->city,
                'bankNumber' => $customer->bank_account,
                'bankName' => '',
            ];
        }
        else
        {
            $data['seller'] = [
                'logo' => '',
                'name' => '',
                'address' => '',
                'nip' => '',
                'city'=>'',
                'bankNumber' => '',
                'bankName'=>'',
            ];
        }

        $data['expenseContents'] = $this->getExpenseContents()->asArray()->all();
        $data['expenseContentRates'] = $this->getExpenseContentRates()->asArray()->all();

        $data['expenseAttachments'] = $this->getExpenseAttachments()->asArray()->all();
        $m = static::find()
            ->joinWith(['events', 'creator'])
            ->asArray()
            ->where(['expense.id'=>$this->id])->one();
        $data['model'] = $m;

        $data['labels'] = [
            'paymentMethod'=>$this->getPaymentmethodLabel(),
            'creator'=>$this->creator->getDisplayLabel(),
        ];
        $data['paymentHistory'] = $this->getPaymentHistoryData();

        $this->data = serialize($data);
        $this->updateAttributes(['data']);
    }

    public function loadData()
    {
        if ($this->data == null)
        {
            $this->storeData();
        }
        $data = unserialize($this->data);
        return $data;
    }

    public function getPaymentHistoryData()
    {
        $data = [];
        $payments = $this->getExpensePaymentHistories()
            ->orderBy(['id'=>SORT_DESC])
            ->all();
        foreach ($payments as $payment)
        {
            $data[] = [
                'id'=> $payment->id,
                'label' => $payment->creator->displayLabel,
                'amount'=>$payment->amount,
                'date'=>$payment->date,
            ];
        }

        return $data;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->needToLink == true)
        {
            $this->linkObjects();
        }

        if (empty($this->events) == false)
        {
            foreach ($this->events as $event)
            {
                $event->updateStatutes(true);
            }
        }
        if ($insert)
        {
            if (empty($this->events) == false)
            {
                foreach ($this->events as $event)
                {
                    Note::createNote(2, 'eventExpenseAdded', $event, $event->id);
                }
            }
                
        }
        Investition::deleteAll(['expense_id'=>$this->id]);
        if (!$insert){
        $type = ExpenseType::find()->where(['id'=>$this->expense_type])->one();
        if ($type->investition)
        {
            foreach ($this->expenseContents as $content)
            {
                $investition = new Investition();
                $investition->expense_id = $this->id;
                $investition->name = $content->name;
                $investition->quantity = (int)$content->count;
                $investition->price = $content->price;
                $investition->total_price= $content->netto;
                $investition->vat= $content->vat;
                $investition->sections = "";
                $investition->datetime = $this->date;
                $investition->save();
            }
            

        }
        }
    }

	public function beforeSave($insert)
	{
		if (empty($this->events) == false)
		{
			foreach ($this->events as $event)
			{
				$name = $event->getDisplayLabel();

				if (preg_match('@'.preg_quote($name, '@').'@', $this->description)==false)
				{
					$this->description = $name."\n".$this->description;
				}
			}

		}
        if (!$insert)
            if ($this->alreadypaid>=$this->total){
                $this->paid = 1;
            }
		return parent::beforeSave($insert); // TODO: Change the autogenerated stub
	}

    public function beforeDelete()
    {
        foreach ($this->events as $event)
        {
            $event->updateStatutes(true);
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }


}
