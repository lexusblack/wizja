<?php

namespace common\models;

use \common\models\base\Province as BaseProvince;

/**
 * This is the model class for table "province".
 */
class Province extends BaseProvince
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string', 'max' => 45]
        ]);
    }

    public static function getList($roles = null)
    {
        $query = self::find()->orderBy('id ASC');

        $list = [];

        $models = $query->all();
        foreach ($models as $model) {
            $list[$model->id] = $model->name;

        }

        return $list;
    }
	
}
