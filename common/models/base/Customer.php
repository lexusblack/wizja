<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "customer".
 *
 * @property integer $id
 * @property string $company
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property string $phone
 * @property string $email
 * @property string $info
 * @property string $create_time
 * @property string $update_time
 * @property integer $type
 * @property integer $status
 * @property string $logo
 * @property string $nip
 * @property string $bank_account
 * @property integer $supplier
 * @property integer $customer
 * @property integer $active
 *
 * @property \common\models\CalendarUserFilter[] $calendarUserFilters
 * @property \common\models\Contact[] $contacts
 * @property \common\models\CustomerDiscountCustomer[] $customerDiscountCustomers
 * @property \common\models\CustomerDiscount[] $customerDiscounts
 * @property \common\models\Event[] $events
 * @property \common\models\EventExpense[] $eventExpenses
 * @property \common\models\Expense[] $expenses
 * @property \common\models\IncomesForCustomer[] $incomesForCustomers
 * @property \common\models\Invoice[] $invoices
 * @property \common\models\Meeting[] $meetings
 * @property \common\models\Offer[] $offers
 * @property \common\models\OutcomesForCustomer[] $outcomesForCustomers
 * @property \common\models\Rent[] $rents
 * @property string $aliasModel
 */
abstract class Customer extends \common\components\BaseActiveRecord
{

    public $groups;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info'], 'string'],
            [['create_time', 'update_time', 'groups'], 'safe'],
            [['type', 'status', 'supplier', 'customer', 'active', 'payment_days', 'customer_type_id'], 'integer'],
            [['company', 'name', 'address', 'city', 'zip', 'phone', 'email', 'logo', 'nip', 'bank_account', 'country'], 'string', 'max' => 255]
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'groups',
            ],
            'relations' => [
                'customerTypes',
            ],
            'modelClasses'=>[
                'common\models\CustomerType',
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Nazwa firmy'),
            'name' => Yii::t('app', 'Nazwa'),
            'address' => Yii::t('app', 'Adres'),
            'city' => Yii::t('app', 'Miasto'),
            'zip' => Yii::t('app', 'Kod pocztowy'),
            'phone' => Yii::t('app', 'Telefon'),
            'email' => Yii::t('app', 'Adres e-mail'),
            'info' => Yii::t('app', 'Dodatkowe informacje'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'type' => Yii::t('app', 'Typ'),
            'status' => Yii::t('app', 'Status'),
            'logo' => Yii::t('app', 'Logo'),
            'nip' => Yii::t('app', 'Numer NIP'),
            'bank_account' => Yii::t('app', 'Numer konta'),
            'supplier' => Yii::t('app', 'Dostawca'),
            'customer' => Yii::t('app', 'Klient'),
            'payment_days'=>Yii::t('app', 'Domyślny termin płatności [dni]'),
            'customer_type_id' => Yii::t('app', 'Typ/Grupa'),
            'country' => Yii::t('app', 'Państwo'),
            'groups' => Yii::t('app', 'Typ/Grupa')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarUserFilters()
    {
        return $this->hasMany(\common\models\CalendarUserFilter::className(), ['customer' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(\common\models\Contact::className(), ['customer_id' => 'id'])->where(['status'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerLogs()
    {
        return $this->hasMany(\common\models\CustomerLog::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerAttachments()
    {
        return $this->hasMany(\common\models\CustomerAttachment::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['customer_id' => 'id'])->orderBy(['status'=>SORT_ASC, 'datetime'=>SORT_ASC]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerNotes()
    {
        return $this->hasMany(\common\models\CustomerNote::className(), ['customer_id' => 'id'])->orderBy(['datetime'=>SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscountCustomers()
    {
        return $this->hasMany(\common\models\CustomerDiscountCustomer::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscounts()
    {
        return $this->hasMany(\common\models\CustomerDiscount::className(), ['id' => 'customer_discount_id'])->viaTable('customer_discount_customer', ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventExpenses()
    {
        return $this->hasMany(\common\models\EventExpense::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(\common\models\Expense::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomesForCustomers()
    {
        return $this->hasMany(\common\models\IncomesForCustomer::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(\common\models\Invoice::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetings()
    {
        return $this->hasMany(\common\models\Meeting::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(\common\models\Offer::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOffers()
    {
        return $this->hasMany(\common\models\AgencyOffer::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForCustomers()
    {
        return $this->hasMany(\common\models\OutcomesForCustomer::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRents()
    {
        return $this->hasMany(\common\models\Rent::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerType()
    {
        return $this->hasOne(\common\models\CustomerType::className(), ['id' => 'customer_type_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerTypes()
    {
        return $this->hasMany(\common\models\CustomerType::className(), ['id' => 'customer_type_id'])->viaTable('customer_group', ['customer_id' => 'id']);
    }


}