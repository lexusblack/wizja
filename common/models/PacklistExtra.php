<?php

namespace common\models;

use Yii;
use \common\models\base\PacklistExtra as BasePacklistExtra;

/**
 * This is the model class for table "packlist_extra".
 */
class PacklistExtra extends BasePacklistExtra
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['packlist_id', 'event_extra_id', 'quantity'], 'integer'],
            [['info'], 'string', 'max' => 255]
        ]);
    }
	
}
