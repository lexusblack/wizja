<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class AssignmentChangeBehavior extends Behavior
{
    protected $_oldValues = [];
    protected $_newValues = [];

    public function events()
    {
        return [
//            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function beforeSave($event)
    {
        return true;
    }
}
