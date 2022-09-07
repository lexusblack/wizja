<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "task_role".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $user_event_role_id
 *
 * @property \common\models\Task $task
 * @property \common\models\UserEventRole $userEventRole
 */
class TaskRole extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'task',
            'userEventRole'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'user_event_role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_role';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'user_event_role_id' => 'User Event Role ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(\common\models\Task::className(), ['id' => 'task_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEventRole()
    {
        return $this->hasOne(\common\models\UserEventRole::className(), ['id' => 'user_event_role_id']);
    }
    

}
