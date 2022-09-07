<?php

namespace common\models;

use Yii;
use \common\models\base\HallAudienceType as BaseHallAudienceType;

/**
 * This is the model class for table "hall_audience_type".
 */
class HallAudienceType extends BaseHallAudienceType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'photo'], 'string', 'max' => 255]
        ]);
    }
	        public function getPhotoUrl()
    {
        if ($this->photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/hall/'.$this->photo);
        }

    }
}
