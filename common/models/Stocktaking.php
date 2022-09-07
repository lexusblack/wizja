<?php

namespace common\models;

use Yii;
use \common\models\base\Stocktaking as BaseStocktaking;

/**
 * This is the model class for table "stocktaking".
 */
class Stocktaking extends BaseStocktaking
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['description'], 'string']
        ]);
    }
	
}
