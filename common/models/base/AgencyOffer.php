<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "agency_offer".
 *
 * @property integer $id
 * @property string $name
 * @property integer $customer_id
 * @property integer $contact_id
 * @property integer $manager_id
 * @property integer $location_id
 * @property string $event_start
 * @property string $event_end
 * @property string $offer_date
 * @property string $payment_date
 * @property integer $event_id
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\Contact $contact
 * @property \common\models\Customer $customer
 * @property \common\models\Event $event
 * @property \common\models\User $manager
 * @property \common\models\AgencyOfferService[] $agencyOfferServices
 * @property \common\models\AgencyOfferServiceCategory[] $agencyOfferServiceCategories
 */
class AgencyOffer extends \yii\db\ActiveRecord
{

    public $schema_id;
    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'contact',
            'customer',
            'event',
            'manager',
            'agencyOfferServices',
            'agencyOfferServiceCategories'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'contact_id', 'manager_id', 'location_id', 'event_id'], 'integer'],
            [['event_start', 'event_end', 'offer_date', 'payment_date', 'create_time', 'update_time', 'provision'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_offer';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Numer'),
            'name' => Yii::t('app', 'Nazwa'),
            'customer_id' => Yii::t('app', 'Klient'),
            'contact_id' => Yii::t('app', 'Osoba kontaktowa'),
            'manager_id' => Yii::t('app', 'Project Manager'),
            'location_id' => Yii::t('app', 'Miejsce'),
            'event_start' => Yii::t('app', 'Początek'),
            'event_end' => Yii::t('app', 'Koniec'),
            'offer_date' => Yii::t('app', 'Data sporządzenia oferty'),
            'payment_date' => Yii::t('app', 'Data płatności'),
            'event_id' => Yii::t('app', 'Wydarzenie'),
            'create_time' => Yii::t('app', 'Data stworzenia'),
            'update_time' => Yii::t('app', 'Data edycji'),
            'schema_id' => Yii::t('app', 'Schemat oferty')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(\common\models\Contact::className(), ['id' => 'contact_id']);
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
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'manager_id']);
    }

    public function getLocation()
    {
        return $this->hasOne(\common\models\Location::className(), ['id' => 'location_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOfferServices()
    {
        return $this->hasMany(\common\models\AgencyOfferService::className(), ['agency_offer_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOfferServiceCategories()
    {
        return $this->hasMany(\common\models\AgencyOfferServiceCategory::className(), ['agency_offer_id' => 'id']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getStatusList()
    {
        $list = [
        0=>'W przygotowaniu',
        1=>'Wysłana',
        2=>'Zaakceptowana'
        ];
        return $list;
    }
}
