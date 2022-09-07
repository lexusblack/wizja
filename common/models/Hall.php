<?php

namespace common\models;

use Yii;
use \common\models\base\Hall as BaseHall;

/**
 * This is the model class for table "hall".
 */
class Hall extends BaseHall
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['area', 'width', 'length', 'height'], 'number'],
            [['name', 'main_photo'], 'string', 'max' => 255]
        ]);
    }
	    public function getPhotoUrl()
    {
        if ($this->main_photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/hall/'.$this->main_photo);
        }

    }
}
