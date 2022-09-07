<?php

namespace common\models\base;
use common\models\EventVehicle;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "vehicle_service".
 *
 * @property integer $id
 * @property integer $vehicle_id
 * @property string $name
 * @property string $description
 * @property integer $status
 * @property string $price
 * @property string $create_time
 * @property string $end_time
 *
 * @property \common\models\Vehicle $vehicle
 */
class VehicleService extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'vehicle'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vehicle_id', 'status'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['create_time', 'end_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_service';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vehicle_id' => 'Vehicle ID',
            'name' => Yii::t('app', 'Tytuł'),
            'description' => Yii::t('app', 'Opis'),
            'status' => 'Status',
            'price' => Yii::t('app', 'Koszt'),
            'create_time' => Yii::t('app', 'Początek'),
            'end_time' => Yii::t('app', 'Koniec'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicle()
    {
        return $this->hasOne(\common\models\Vehicle::className(), ['id' => 'vehicle_id']);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        //$this->getNoItemsItem();
        parent::afterSave($insert, $changedAttributes);
        if ($this->status == 1)
        {
                $this->vehicle->status = 0;
                $this->vehicle->save();
                if ($this->end_time)
                        $events = EventVehicle::find()->where(['vehicle_id'=>$this->vehicle_id])->andWhere(['>', 'end_time', $this->create_time])->andWhere(['<', 'start_time', $this->end_time])->all();
                else 
                        $events = EventVehicle::find()->where(['vehicle_id'=>$this->vehicle_id])->andWhere(['>', 'end_time', $this->create_time])->all();
                foreach ($events as $event)
                {
                    $attributes = ['vehicle_id'=>$event->vehicle_id, 'event_id'=>$event->event_id];
                    EventVehicleWorkingHours::deleteAll($attributes);
                        EventVehicle::deleteAll($attributes);
                }
        }else{
                $this->vehicle->status = 1;
                $this->vehicle->save();
        }

    }
}
