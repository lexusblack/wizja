<?php

namespace common\models;

use Yii;
use \common\models\base\Skill as BaseSkill;

/**
 * This is the model class for table "skill".
 */
class Skill extends BaseSkill
{
	public static function getList($roles=null) 
    {
        $query = self::find()->orderBy('name ASC');
		
        $list = [];
        
		$models = $query->all();
        foreach ($models as $model) 
        {
            $list[$model->id] = $model->name;
            
        }
        
        return $list;
    }
}
