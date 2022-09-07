<?php

namespace common\models;

use Yii;
use \common\models\base\GearFavorite as BaseGearFavorite;

/**
 * This is the model class for table "gear_favorite".
 */
class GearFavorite extends BaseGearFavorite
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'user_id', 'position'], 'integer']
        ]);
    }
	
}
