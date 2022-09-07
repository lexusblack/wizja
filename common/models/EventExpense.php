<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\EventExpense as BaseEventExpense;
use yii\web\HttpException;

/**
 * This is the model class for table "event_expense".
 */
class EventExpense extends BaseEventExpense
{
    const STATUS_INVOICE_NOT_BOOKED = 1;
    const STATUS_INVOICE_AWAITING = 5;
    const STATUS_INVOICE_PAYED = 8;
    const STATUS_INVOICE_BOOKED = 10;
    const STATUS_NO_INVOICE = 99;
    const STATUS_INTERNAL = 100;

    const TYPE_SINGLE = 1;
    const TYPE_GROUP = 2;

    public $departmentIds;
    public $sections;

    public $dateRange;

    public static function getStatusList()
    {
        $list = [
            self::STATUS_INVOICE_NOT_BOOKED => Yii::t('app', 'faktura niezaksięgowana'),
            self::STATUS_INVOICE_BOOKED => Yii::t('app', 'faktura zaksięgowana'),
            self::STATUS_INVOICE_PAYED => Yii::t('app', 'faktura zapłacona'),
            self::STATUS_INVOICE_AWAITING => Yii::t('app', 'oczekiwanie na fakturę'),
            self::STATUS_NO_INVOICE => Yii::t('app', 'brak faktury'),
            self::STATUS_INTERNAL => Yii::t('app', 'koszt wewnętrzny'),
        ];
        return $list;
    }

    public function behaviors()
    {
        $behaviors = [

            'eventDatesBehavior' => [
                'class'=>\common\behaviors\EventDatesBehavior::className(),
            ],

        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function rules()
    {
        $rules = [
            [['departmentIds'], 'each', 'rule'=>['integer']],
            [['sections'], 'each', 'rule'=>['string']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [
            'departmentIds'=>Yii::t('app', 'Oddziały'),
            'dateRange'=>Yii::t('app', 'Daty'),
            'sections' => Yii::t('app', 'Sekcje'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getStatusLabel()
    {
        $list = static::getStatusList();
        $index = $this->status;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function beforeSave($insert)
    {
        if ($this->amount_customer==null)
        {
            $this->amount_customer=0;
        }
        if ($this->amount==null)
        {
            $this->amount=0;
        }
        if ($this->profit == null)
        {
            $this->profit = $this->amount_customer - $this->amount;
        }

        if (sizeof($this->sections) > 1)
        {
            $this->type = self::TYPE_GROUP;
            $this->section = implode(';', $this->sections);
        }
        else
        {
            if (sizeof($this->sections) > 0)
            {
                $this->section = ArrayHelper::getValue($this->sections, 0, null);
            }
            $this->type = self::TYPE_SINGLE;
            
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //kasujemy grupowe
        static::deleteAll([
            'group_id'=>$this->id,
        ]);
        if ($this->type == self::TYPE_GROUP && $this->group_id==null)
        {
            $departmentCount = sizeof($this->departmentIds);
            $sectionCount = sizeof($this->sections);
            foreach ($this->sections as $section)
            {
                $model = new static($this->attributes);
                $model->sections = [$section];
                $model->id = null;
                $model->type = self::TYPE_SINGLE;
                $model->amount = round(($this->amount/$sectionCount), 2);
                $model->amount_customer = round(($this->amount_customer/$sectionCount), 2);
                $model->profit = round(($this->profit/$sectionCount), 2);
                $model->group_id = $this->id;
                $model->group_amount = $this->amount;
                $model->group_amount_customer = $this->amount_customer;


                if ($model->save() == false)
                {
                    var_dump($model->errors); die;
                }
            }
        }

        if ($this->event !== null)
        {
            $this->event->updateStatutes(true);
            if ($this->event->type!=1)
            {
                $this->event->updateParentExpense();
            }
        }
        if ($insert)
        {
            if (($this->event !== null)&&($this->group_id==null))
                Note::createNote(2, 'eventCostAdded', $this, $this->event_id);
        }else{
            if ($this->gear_id)
            {
                if ((isset($changedAttributes['amount']))&&($this->amount!=$changedAttributes['amount']))
                {
                    $eog = EventOuterGear::find()->where(['event_id'=>$this->event_id, 'outer_gear_id'=>$this->gear_id])->one();
                    $eog->price = $this->amount;
                    $eog->save();
                }
            }
        }
    }


    public function beforeDelete()
    {
        Note::createNote(2, 'eventCostDeleted', $this, $this->event_id);
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->event !== null)
        {
            if ($this->event->type!=1)
            {
                $this->event->updateParentExpense();
            }
        }
    }

    public function loadDepartmetns()
    {
        throw new HttpException(400, Yii::t('app', 'Zmiana na sekcje ').__METHOD__);
    }

    public function loadSections()
    {
        if ($this->type == self::TYPE_SINGLE)
        {
            $this->sections = [$this->section];
        }
        else if ($this->isNewRecord == false)
        {
            $sections = static::find()
                ->select(['section'])
                ->where([
                    'group_id'=>$this->id,
                ])
                ->column();
            $this->sections = array_unique(array_filter($sections));
        }
    }

    public function getDepartmentsLabel()
    {
        throw new HttpException(400, Yii::t('app', 'Zmiana na sekcje ').__METHOD__);
    }

    public function getSectionsLabel()
    {

        $this->loadSections();
        return implode('; ', $this->sections);
    }

    public static function getSectionList()
    {
        $list = [
            Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa'),
            Yii::t('app', 'Inne') => Yii::t('app', 'Inne'),
            Yii::t('app', 'Transport') => Yii::t('app', 'Transport'),
        ];
        $departmets = ArrayHelper::map(GearCategory::find()->where(['lvl'=>1])->andWhere(['active'=>1])->asArray()->all(), 'name', 'name');
        $list = ArrayHelper::merge($list, $departmets);
//        ksort($list);

        return $list;
    }

}
