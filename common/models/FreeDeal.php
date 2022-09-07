<?php

namespace common\models;

use Yii;
use \common\models\base\FreeDeal as BaseFreeDeal;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "deal".
 */
class FreeDeal extends BaseFreeDeal
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

    public function getList($or=null)
    {
 
            return ArrayHelper::map(FreeDeal::find()->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'name', 'name');

    }
	
}
