<?php

namespace common\models;

use Yii;
use \common\models\base\VehicleService as BaseVehicleService;

/**
 * This is the model class for table "vehicle_service".
 */
class VehicleService extends BaseVehicleService
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['vehicle_id', 'status'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['create_time', 'end_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            Note::createNote(4, 'vehicleService', $this->vehicle, $this->id);
        }else{
            if ((isset($changedAttributes['status']))&&($changedAttributes['status']!=$this->status)&&($this->status==2))
                    Note::createNote(4, 'vehicleServiceBack', $this->vehicle, $this->id);
        }

    }
	
}
