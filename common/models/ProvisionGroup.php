<?php

namespace common\models;

use Yii;
use \common\models\base\ProvisionGroup as BaseProvisionGroup;

/**
 * This is the model class for table "provision_group".
 */
class ProvisionGroup extends BaseProvisionGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['team_id', 'level', 'type', 'is_pm', 'customer_group_id', 'main_only', 'add_to_users'], 'integer'],
            [['provision'], 'number'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public function getTypes()
    {
        return [
        1 => Yii::t('app', 'Od zysku'),
        2=> Yii::t('app', 'Od wartości'),
        3=> Yii::t('app', 'Od kosztów'),
        ];
    }
	
}
