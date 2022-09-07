<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "task".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $datetime
 * @property integer $order
 * @property string $create_time
 * @property string $update_time
 * @property integer $creator_id
 * @property integer $type
 * @property integer $status
 * @property integer $event_id
 * @property string $color
 * @property string $comment
 * @property integer $task_category_id
 *
 * @property \common\models\User $creator
 * @property \common\models\TaskCategory $taskCategory
 * @property \common\models\TaskDone[] $taskDones
 * @property \common\models\TaskNotification[] $taskNotifications
 * @property \common\models\TaskNotificationRole[] $taskNotificationRoles
 * @property \common\models\TaskRole[] $taskRoles
 * @property \common\models\UserTask[] $userTasks
 * @property \common\models\User[] $users
 */
class Task extends \yii\db\ActiveRecord
{



    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'creator',
            'taskCategory',
            'taskDones',
            'taskNotifications',
            'taskNotificationRoles',
            'taskRoles',
            'userTasks',
            'users'
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['datetime', 'create_time', 'update_time', 'from'], 'safe'],
            [['order', 'creator_id', 'type', 'status', 'event_id', 'task_category_id', 'only_one', 'customer_id', 'department_id', 'task_id'], 'integer'],
            [['title', 'comment'], 'string', 'max' => 255],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'datetime' => 'Datetime',
            'order' => 'Order',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'creator_id' => 'Creator ID',
            'type' => 'Type',
            'status' => 'Status',
            'event_id' => 'Event ID',
            'color' => 'Color',
            'comment' => 'Comment',
            'task_category_id' => 'Task Category ID',
            'customer_id'=>Yii::t('app', 'Powiązany klient'),
            'department_id'=>Yii::t('app', 'Powiązany dział'),
            'task_id'=>Yii::t('app', 'Zadanie nadrzędne')
        ];
    }


    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }

/**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(\common\models\Department::className(), ['id' => 'department_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskCategory()
    {
        return $this->hasOne(\common\models\TaskCategory::className(), ['id' => 'task_category_id']);
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
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    } 
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(\common\models\Project::className(), ['id' => 'project_id']);
    }      
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskDones()
    {
        return $this->hasMany(\common\models\TaskDone::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskNotes()
    {
        return $this->hasMany(\common\models\TaskNote::className(), ['task_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['task_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAttachments()
    {
        return $this->hasMany(\common\models\TaskAttachment::className(), ['task_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskNotifications()
    {
        return $this->hasMany(\common\models\TaskNotification::className(), ['task_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskNotificationRoles()
    {
        return $this->hasMany(\common\models\TaskNotificationRole::className(), ['task_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskRoles()
    {
        return $this->hasMany(\common\models\TaskRole::className(), ['task_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTasks()
    {
        return $this->hasMany(\common\models\UserTask::className(), ['task_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('user_task', ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('task_notification_user', ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'user_event_role'])->viaTable('task_notification_role', ['task_id' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'user_event_role_id'])->viaTable('task_role', ['task_id' => 'id']);
    }    

}
