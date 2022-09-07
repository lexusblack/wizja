<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "customer_note".
 *
 * @property integer $id
 * @property string $name
 * @property string $datetime
 * @property string $type
 * @property integer $customer_id
 * @property integer $client_id
 * @property integer $event_id
 * @property integer $rent_id
 *
 * @property \common\models\ClientNoteAttachment[] $clientNoteAttachments
 * @property \common\models\Customer $client
 * @property \common\models\Event $event
 * @property \common\models\Rent $rent
 */
class CustomerNote extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'clientNoteAttachments',
            'client',
            'event',
            'rent'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['datetime', 'next_date'], 'safe'],
            [['customer_id', 'contact_id', 'user_id', 'event_id', 'rent_id', 'meeting_id'], 'integer'],
            [['type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_note';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Treść'),
            'datetime' => Yii::t('app', 'Data'),
            'type' => Yii::t('app', 'Typ'),
            'customer_id' =>Yii::t('app',  'Klient'),
            'contact_id' =>Yii::t('app',  'Kontakt'),
            'event_id' =>Yii::t('app',  'Event'),
            'rent_id' =>Yii::t('app',  'Wypożyczenie'),
            'user_id' =>Yii::t('app',  'Użytkownik'),
            'meeting_id' =>Yii::t('app',  'Spotkanie'),
            'next_date' =>Yii::t('app',  'Ustaw planowaną datę kolejnej aktywności'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientNoteAttachments()
    {
        return $this->hasMany(\common\models\ClientNoteAttachment::className(), ['client_note_id' => 'id']);
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
    public function getContact()
    {
        return $this->hasOne(\common\models\Contact::className(), ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
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
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(\common\models\Meeting::className(), ['id' => 'meeting_id']);
    }  

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(\common\models\Offer::className(), ['id' => 'offer_id']);
    }   
}
