<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerType as BaseCustomerType;

/**
 * This is the model class for table "customer_type".
 */
class CustomerType extends BaseCustomerType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'required'],
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique']
        ]);
    }
	
    public static function getList()
    {
        $all = CustomerType::find()->where(['active'=>1])->all();
        $list = [];
        foreach ($all as $ct)
        {
            $list[$ct->id] = $ct->name;
        }
        return $list;
    }
}
