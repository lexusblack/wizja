<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_offer_vehicle".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $vehicle_model_id
 * @property integer $quantity
 * @property string $schedule
 */
class EventOfferVehicle extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'vehicle_model_id', 'quantity'], 'integer'],
            [['schedule'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_offer_vehicle';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'vehicle_model_id' => 'Vehicle Model ID',
            'quantity' => 'Quantity',
            'schedule' => 'Schedule',
        ];
    }

        public function getVehicle()
    {
        return $this->hasOne(\common\models\VehicleModel::className(), ['id' => 'vehicle_model_id']);
    } 
}
