<?php

namespace common\models;

use Yii;
use \common\models\base\GearCategoryTranslate as BaseGearCategoryTranslate;

/**
 * This is the model class for table "gear_category_translate".
 */
class GearCategoryTranslate extends BaseGearCategoryTranslate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_category_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 45]
        ]);
    }
	
}
