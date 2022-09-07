<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "task_schema".
 *
 * @property integer $id
 * @property integer $tasks_schema_cat_id
 * @property string $name
 * @property string $description
 * @property integer $order
 *
 * @property \common\models\TasksSchemaCat $tasksSchemaCat
 */
class TaskSchema extends \yii\db\ActiveRecord
{
    const NONE = 1;
    const BEFORE_START = 2;
    const AFTER_START = 3;
    const BEFORE_END = 4;
    const AFTER_END = 5;

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'tasksSchemaCat'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_schema_cat_id', 'order', 'time_type', 'days', 'hours', 'minutes', 'only_one', 'manager', 'manager_notification'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_schema';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tasks_schema_cat_id' => 'Tasks Schema Cat ID',
            'name' => Yii::t('app', 'Nazwa'),
            'description' => Yii::t('app', 'Opis'),
            'order' => 'Order',
            'time_type' => Yii::t('app', 'Czas wykonania zadania'),
            'days' => Yii::t('app', 'dni'),
            'hours' => Yii::t('app', 'godzin'),
            'minutes' => Yii::t('app', 'minut'),
            'manager' => Yii::t('app', 'Project manager przypisany do zadania'),
            'manager_notification' => Yii::t('app', 'Powiadomenie o wykonaniu do project managera')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksSchemaCat()
    {
        return $this->hasOne(\common\models\TasksSchemaCat::className(), ['id' => 'tasks_schema_cat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('task_schema_user', ['task_schema_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(\common\models\Team::className(), ['id' => 'team_id'])->viaTable('task_schema_team', ['task_schema_id' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'role_id'])->viaTable('task_schema_role', ['task_schema_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('task_schema_notification_user', ['task_schema_id' => 'id']);
    }

    public function getNotificationRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'role_id'])->viaTable('task_schema_notification_role', ['task_schema_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskSchemaNotifications()
    {
        return $this->hasMany(\common\models\TaskSchemaNotification::className(), ['task_schema_id' => 'id']);
    }

    public function getTimeTypes()
    {
        $types = [
            static::NONE => Yii::t('app', 'Brak'),
            static::BEFORE_START => Yii::t('app', 'Przed startem wydarzenia'),
            static::AFTER_START => Yii::t('app', 'Po starcie wydarzenia'),
            static::BEFORE_END => Yii::t('app', 'Przed końcem wydarzenia'),
            static::AFTER_END => Yii::t('app', 'Po końcu wydarzenia')
        ];
        return $types;
    }
}
