<?php

namespace common\models;

use Yii;
use \common\models\base\RolePriceGroup as BaseRolePriceGroup;

/**
 * This is the model class for table "role_price_group".
 */
class RolePriceGroup extends BaseRolePriceGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['currency', 'unit'], 'string', 'max' => 45]
        ]);
    }

        public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            $roles = UserEventRole::find()->where(['active'=>1])->all();
            foreach ($roles as $r)
            {
                $price = new RolePrice();
                $price->role_id = $r->id;
                $price->role_price_group_id = $this->id;
                $price->cost = $r->salary;
                $price->cost_hour = $r->salary_hours;
                $price->price = $r->salary_customer;
                $price->default = 0;
                $price->save();
            }
        }
    }

    public function getList()
    {
        return \common\helpers\ArrayHelper::map(\common\models\RolePriceGroup::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
    }


	
}
