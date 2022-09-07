<?php

namespace common\models;

use Yii;
use \common\models\base\Country as BaseCountry;

/**
 * This is the model class for table "country".
 */
class Country extends BaseCountry
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

    public static function getList($roles = null)
    {
        $query = self::find()->orderBy('name ASC');

        $list = [];

        $models = $query->all();
        foreach ($models as $model) {
            $list[$model->id] = $model->name;

        }

        return $list;
    }
	
}
