<?php

namespace common\models;

use Yii;
use \common\models\base\GearAttachemntType as BaseGearAttachemntType;

/**
 * This is the model class for table "gear_attachment_type".
 */
class GearAttachemntType extends BaseGearAttachemntType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
