<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_task".
 *
 * @property integer $event_id
 * @property integer $task_id
 */
class EventTask extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'task_id'], 'required'],
            [['event_id', 'task_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_task';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => 'Event ID',
            'task_id' => 'Task ID',
        ];
    }
}
