<?php

namespace common\models;

use \common\models\base\Spaceplanner as BaseSpaceplanner;

/**
 * This is the model class for table "spaceplanner".
 */
class Spaceplanner extends BaseSpaceplanner
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id'], 'integer'],
            [['snapshot'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255]
        ]);
    }
	
}
