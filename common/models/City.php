<?php

namespace common\models;
use common\helpers\ArrayHelper;

use Yii;
use \common\models\base\City as BaseCity;

/**
 * This is the model class for table "city".
 */
class City extends BaseCity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public function getList()
    {
        return ArrayHelper::map(City::find()->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'id', 'name');

    }
	
}
