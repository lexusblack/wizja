<?php

namespace common\models;

use Yii;
use \common\models\base\GearServiceHistory as BaseGearServiceHistory;

/**
 * This is the model class for table "gear_service_history".
 */
class GearServiceHistory extends BaseGearServiceHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_service_id', 'user_id', 'statut_from', 'statut_to'], 'integer'],
            [['datetime'], 'safe'],
            [['description'], 'string', 'max' => 255]
        ]);
    }
	
}
