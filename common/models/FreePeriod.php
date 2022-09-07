<?php

namespace common\models;

use Yii;
use \common\models\base\FreePeriod as BaseFreePeriod;

/**
 * This is the model class for table "period".
 */
class FreePeriod extends BaseFreePeriod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
