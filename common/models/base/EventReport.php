<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_report".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $manager_id
 * @property string $name
 * @property string $code
 * @property integer $customer_id
 * @property string $event_start
 * @property string $event_end
 * @property integer $status
 * @property string $location
 * @property string $paying_date
 * @property string $total_value
 * @property string $total_cost
 * @property string $total_provision
 * @property string $total_predicted_cost
 * @property string $total_predicted_provision
 * @property integer $event_model_id
 * @property integer $event_type_id
 *
 * @property \common\models\Customer $customer
 * @property \common\models\Event $event
 * @property \common\models\User $manager
 * @property \common\models\EventType $eventModel
 */
class EventReport extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'customer',
            'event',
            'manager',
            'eventModel'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'manager_id', 'customer_id', 'status', 'event_model_id', 'event_type_id'], 'integer'],
            [['event_start', 'event_end', 'paying_date'], 'safe'],
            [['total_value', 'total_cost', 'total_provision', 'total_predicted_cost', 'total_predicted_provision'], 'number'],
            [['name', 'location'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_report';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'manager_id' => 'PM',
            'name' => Yii::t('app', 'Nazwa'),
            'code' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Kontrahent'),
            'event_start' => Yii::t('app', 'Od'),
            'event_end' => Yii::t('app', 'Do'),
            'status' => Yii::t('app', 'Status'),
            'location' => Yii::t('app', 'Adres'),
            'paying_date' => Yii::t('app', 'Miesiąc księgowania'),
            'total_value' => Yii::t('app', 'Wartość'),
            'total_cost' => Yii::t('app', 'Koszty'),
            'total_provision' => Yii::t('app', 'Suma prowizji'),
            'total_predicted_cost' => Yii::t('app', 'Przewidywane koszty'),
            'total_predicted_provision' => Yii::t('app', 'Przewidywane prowizje'),
            'event_model_id' => Yii::t('app', 'Rodzaj'),
            'event_type_id' => Yii::t('app', 'Typ'),
            'prepaid'=>Yii::t('app', 'Zaliczka'),
            'paid'=>Yii::t('app', 'Zapłacono'),
        ];
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
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventModel()
    {
        return $this->hasOne(\common\models\EventType::className(), ['id' => 'event_model_id']);
    }
    }
