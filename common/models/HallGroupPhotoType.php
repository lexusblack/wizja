<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupPhotoType as BaseHallGroupPhotoType;

/**
 * This is the model class for table "hall_group_photo_type".
 */
class HallGroupPhotoType extends BaseHallGroupPhotoType
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
