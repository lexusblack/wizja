<?php

namespace common\models;

use \common\models\base\LocationType as BaseLocationType;

/**
 * This is the model class for table "location_type".
 */
class LocationType extends BaseLocationType
{
        use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 45]
        ]);
    }

    public static function getList($term=null)
    {
        if ($term)
        {
                $models = static::find()->where(['like', 'name', $term])->orderBy(['name'=>SORT_ASC])->all();
        }else{
            $models = static::find()->orderBy(['name'=>SORT_ASC])->all();
        }
        
            
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = strtolower($model->name);
        }

        return $list;
    }
    public function getDisplayLabel()
    {
        return $this->name;
    }	
}
