<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base-model class for table "expense".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $unit
 * @property string $netto
 * @property string $brutto
 * @property string $lumpcode
 * @property string $type
 * @property string $classification
 * @property integer $discount
 * @property string $description
 * @property integer $notes
 * @property integer $documents
 * @property string $tags
 * @property string $create_time
 * @property string $update_time
 * @property string $count
 * @property integer $customer_id
 * @property string $number
 * @property string $paymentmethod
 * @property string $alreadypaid
 * @property string $alreadypaid_initial
 * @property string $remaining
 * @property string $payment_date
 * @property string $paymentstate
 * @property string $disposaldate
 * @property string $date
 * @property string $paymentdate
 * @property string $currency
 * @property string $currency_exchange
 * @property string $currency_label
 * @property string $currency_date
 * @property string $price_currency_exchange
 * @property string $good_price_group_currency_exchange
 * @property integer $expense_type
 * @property string $vat
 * @property string $total
 * @property string $tax
 * @property integer $paid
 * @property integer $owner_id
 * @property string $owner_class
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property resource $data
 * @property integer $paymentmethod_id
 * @property integer $creator_id
 *
 * @property \common\models\EventExpense[] $eventExpenses
 * @property \common\models\Customer $customer
 * @property \common\models\Paymentmethod $paymentmethod0
 * @property \common\models\User $creator
 * @property \common\models\ExpenseAttachment[] $expenseAttachments
 * @property \common\models\ExpenseContent[] $expenseContents
 * @property \common\models\ExpenseContentRate[] $expenseContentRates
 * @property \common\models\ExpenseEvent[] $expenseEvents
 * @property \common\models\Event[] $events
 * @property \common\models\ExpensePaymentHistory[] $expensePaymentHistories
 * @property string $aliasModel
 */
abstract class Expense extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expense';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['netto', 'brutto', 'lumpcode', 'count', 'alreadypaid', 'alreadypaid_initial', 'remaining', 'currency_exchange', 'price_currency_exchange', 'good_price_group_currency_exchange', 'vat', 'total', 'tax'], 'number'],
            [['discount', 'notes', 'documents', 'customer_id', 'expense_type', 'paid', 'owner_id', 'year', 'month', 'day', 'paymentmethod_id'], 'integer'],
            [['description', 'data'], 'string'],
            [['create_time', 'update_time', 'payment_date', 'disposaldate', 'date', 'paymentdate', 'currency_date'], 'safe'],
            [['name', 'code', 'unit', 'type', 'classification', 'tags', 'number', 'paymentmethod', 'paymentstate', 'currency', 'currency_label', 'owner_class'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['paymentmethod_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paymentmethod::className(), 'targetAttribute' => ['paymentmethod_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Opis wydatku'),
            'code' => Yii::t('app', 'Kod produktu'),
            'unit' => Yii::t('app', 'Jednostka'),
            'netto' => Yii::t('app', 'Cena netto'),
            'brutto' => Yii::t('app', 'Cena brutto'),
            'lumpcode' => Yii::t('app', 'Stawka podatku od przychodu'),
            'type' => Yii::t('app', 'Typ'),
            'classification' => Yii::t('app', 'Kod klasyfikacji PKWiU'),
            'discount' => Yii::t('app', 'Rabat'),
            'description' => Yii::t('app', 'Opis'),
            'notes' => Yii::t('app', 'Liczba notatek'),
            'documents' => Yii::t('app', 'Liczba dokument??w'),
            'tags' => Yii::t('app', 'Tagi'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'count' => Yii::t('app', 'Ilo???? produktu'),
            'customer_id' => Yii::t('app', 'Kontrahent'),
            'number' => Yii::t('app', 'Numer dokumentu'),
            'paymentmethod' => Yii::t('app', 'Metoda p??atno??ci'),
            'alreadypaid' => Yii::t('app', 'Zap??acono'),
            'alreadypaid_initial' => Yii::t('app', 'Zap??acono'),
            'remaining' => Yii::t('app', 'Pozosta??o do zap??aty'),
            'payment_date' => Yii::t('app', 'Zap??acono dnia'),
            'paymentstate' => Yii::t('app', 'Stan p??atno??ci'),
            'disposaldate' => Yii::t('app', 'Data odbioru faktury'),
            'date' => Yii::t('app', 'Data wystawienia'),
            'paymentdate' => Yii::t('app', 'Termin p??atno??ci'),
            'currency' => Yii::t('app', 'Waluta'),
            'currency_exchange' => Yii::t('app', 'Kurs ksi??gowy'),
            'currency_label' => Yii::t('app', 'Numer tabeli NBP kursu ksi??gowego'),
            'currency_date' => Yii::t('app', 'Data opublikowania kursu'),
            'price_currency_exchange' => Yii::t('app', 'Kurs stosowany przy przeliczaniu cen'),
            'good_price_group_currency_exchange' => Yii::t('app', 'Kurs grupy cenowej stosowany przy przeliczaniu cen'),
            'expense_type' => Yii::t('app', 'Rodzaj wydatku'),
            'vat' => Yii::t('app', 'Stawka Vat'),
            'total' => Yii::t('app', 'Razem'),
            'tax' => Yii::t('app', 'Warto???? VAT'),
            'paid' => Yii::t('app', 'Zap??acono ca??o????'),
            'owner_id' => Yii::t('app', 'ID w??a??ciciela'),
            'owner_class' => Yii::t('app', 'Klasa w??a??ciciela'),
            'year' => Yii::t('app', 'Rok'),
            'month' => Yii::t('app', 'Miesi??c'),
            'day' => Yii::t('app', 'Dzie??'),
            'data' => Yii::t('app', 'Data'),
            'paymentmethod_id' => Yii::t('app', 'Metoda p??atno??ci'),
            'creator_id' => Yii::t('app', 'Wystawi??'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventExpenses()
    {
        return $this->hasMany(\common\models\EventExpense::className(), ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentmethod0()
    {
        return $this->hasOne(\common\models\Paymentmethod::className(), ['id' => 'paymentmethod_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseAttachments()
    {
        return $this->hasMany(\common\models\ExpenseAttachment::className(), ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseContents()
    {
        return $this->hasMany(\common\models\ExpenseContent::className(), ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseContentRates()
    {
        return $this->hasMany(\common\models\ExpenseContentRate::className(), ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseEvents()
    {
        return $this->hasMany(\common\models\ExpenseEvent::className(), ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['id' => 'event_id'])->viaTable('expense_event', ['expense_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpensePaymentHistories()
    {
        return $this->hasMany(\common\models\ExpensePaymentHistory::className(), ['expense_id' => 'id']);
    }




}
