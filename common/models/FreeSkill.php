<?php

namespace common\models;

use Yii;
use \common\models\base\FreeSkill as BaseFreeSkill;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "skill".
 */
class FreeSkill extends BaseFreeSkill
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
    public function getList($or=null)
    {
        if ($or)
            return ArrayHelper::map(FreeSkill::find()->where(['active'=>1])->orWhere(['name'=>$or])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'name', 'name');
        else
            return ArrayHelper::map(FreeSkill::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'name', 'name');

    }
}
