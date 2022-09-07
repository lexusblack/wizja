<?php

namespace common\models;

use Yii;
use \common\models\base\VehicleTranslate as BaseVehicleTranslate;

/**
 * This is the model class for table "gear_translate".
 */
class VehicleTranslate extends BaseVehicleTranslate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['vehicle_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45]
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(\common\models\Language::className(), ['code' => 'language_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicle()
    {
        return $this->hasOne(\common\models\Vehicle::className(), ['id' => 'vehicle_id']);
    }
}
