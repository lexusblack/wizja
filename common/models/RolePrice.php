<?php

namespace common\models;

use Yii;
use \common\models\base\RolePrice as BaseRolePrice;

/**
 * This is the model class for table "role_price".
 */
class RolePrice extends BaseRolePrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['role_id', 'default'], 'integer'],
            [['price', 'cost_hour', 'cost'], 'number']
        ]);
    }

        public function getList($role_id=null, $currency=null)
    {
        if ($role_id)
                return \common\helpers\ArrayHelper::map(RolePrice::find()->where(['role_id'=>$role_id, 'currency'=>$currency])->asArray()->all(), 'id', 'name');
            else
                return \common\helpers\ArrayHelper::map(RolePrice::find()->asArray()->all(), 'id', 'name');
    } 
	
}
