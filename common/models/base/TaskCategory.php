<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "task_category".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property integer $event_id
 * @property integer $order
 *
 * @property \common\models\Task[] $tasks
 * @property \common\models\Event $event
 */
class TaskCategory extends \yii\db\ActiveRecord
{

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'tasks',
            'event'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'event_id', 'order'], 'integer'],
            [['name', 'color'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_category';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'event_id' => 'Event ID',
            'order' => 'Order',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['task_category_id' => 'id'])->orderBy(['order'=>SORT_ASC]);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
    

}
