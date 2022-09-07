<?php

namespace common\models;

use Yii;
use \common\models\base\OuterGearFavorite as BaseOuterGearFavorite;

/**
 * This is the model class for table "outer_gear_favorite".
 */
class OuterGearFavorite extends BaseOuterGearFavorite
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['outer_gear_id', 'user_id', 'position'], 'integer']
        ]);
    }
	
}
