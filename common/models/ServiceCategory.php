<?php

namespace common\models;

use Yii;
use \common\models\base\ServiceCategory as BaseServiceCategory;

/**
 * This is the model class for table "service_category".
 */
class ServiceCategory extends BaseServiceCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['in_offer', 'position'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'color'], 'string', 'max' => 255],
        ]);
    }
	
}
