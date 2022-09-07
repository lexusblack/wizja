<?php

namespace common\models;

use Yii;
use \common\models\base\FreeDevice as BaseFreeDevice;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "device".
 */
class FreeDevice extends BaseFreeDevice
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
            return ArrayHelper::map(FreeDevice::find()->where(['active'=>1])->orWhere(['name'=>$or])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'name', 'name');
        else 
            return ArrayHelper::map(FreeDevice::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'name', 'name');

    }
	
}
