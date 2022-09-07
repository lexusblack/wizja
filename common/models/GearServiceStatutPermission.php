<?php

namespace common\models;

use Yii;
use \common\models\base\GearServiceStatutPermission as BaseGearServiceStatutPermission;

/**
 * This is the model class for table "gear_service_statut_permission".
 */
class GearServiceStatutPermission extends BaseGearServiceStatutPermission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_service_statut_id'], 'integer'],
            [['permission_group_id'], 'string', 'max' => 255]
        ]);
    }
	
}
