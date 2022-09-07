<?php

namespace common\models;

use Yii;
use \common\models\base\PacklistOuterGear as BasePacklistOuterGear;

/**
 * This is the model class for table "packlist_outer_gear".
 */
class PacklistOuterGear extends BasePacklistOuterGear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['packlist_id', 'event_outer_gear', 'quantity'], 'integer'],
            [['info'], 'string', 'max' => 255]
        ]);
    }

        public function afterDelete()
    {
            parent::afterDelete();
            $gear = EventOuterGear::findOne($this->event_outer_gear);
            if ($gear)
                $gear->updateCount();
            
    }
	
}
