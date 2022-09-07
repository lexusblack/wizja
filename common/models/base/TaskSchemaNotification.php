<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "task_schema_notification".
 *
 * @property integer $id
 * @property integer $task_schema_id
 * @property integer $time_type
 * @property integer $time
 * @property integer $email
 * @property integer $sms
 * @property integer $push
 *
 * @property \common\models\TaskSchema $taskSchema
 */
class TaskSchemaNotification extends \yii\db\ActiveRecord
{
            const MINUTES_BEFORE = 1;
            const HOURS_BEFORE = 2;
            const DAYS_BEFORE = 3;
            const MINUTES_AFTER = 4;
            const HOURS_AFTER = 5;
            const DAYS_AFTER = 6;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'taskSchema'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_schema_id', 'time_type', 'time', 'email', 'sms', 'push'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_schema_notification';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_schema_id' => 'Task Schema ID',
            'time_type' => 'Time Type',
            'time' => 'Time',
            'email' => 'Email',
            'sms' => 'Sms',
            'push' => 'Push',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskSchema()
    {
        return $this->hasOne(\common\models\TaskSchema::className(), ['id' => 'task_schema_id']);
    }
    


    public function getTimeTypes()
    {
        $types = [
            static::MINUTES_BEFORE => Yii::t('app', 'minut wcześniej'),
            static::HOURS_BEFORE => Yii::t('app', 'godzin wcześniej'),
            static::DAYS_BEFORE => Yii::t('app', 'dni wcześniej'),
            static::MINUTES_AFTER => Yii::t('app', 'minut później'),
            static::HOURS_AFTER => Yii::t('app', 'godzin później'),
            static::DAYS_AFTER => Yii::t('app', 'dni później'),
        ];
        return $types;
    }
}
