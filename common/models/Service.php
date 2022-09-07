<?php

namespace common\models;

use Yii;
use \common\models\base\Service as BaseService;

/**
 * This is the model class for table "service".
 */
class Service extends BaseService
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['service_category_id', 'in_offer', 'position'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
