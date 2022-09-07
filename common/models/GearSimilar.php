<?php

namespace common\models;

use Yii;
use \common\models\base\GearSimilar as BaseGearSimilar;

/**
 * This is the model class for table "gear_similar".
 */
class GearSimilar extends BaseGearSimilar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'similar_id'], 'integer']
        ]);
    }
	
}
