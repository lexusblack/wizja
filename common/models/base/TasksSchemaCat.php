<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "tasks_schema_cat".
 *
 * @property integer $id
 * @property integer $tasks_schema_id
 * @property string $name
 * @property integer $order
 *
 * @property \common\models\TaskSchema[] $taskSchemas
 * @property \common\models\TasksSchema $tasksSchema
 */
class TasksSchemaCat extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'taskSchemas',
            'tasksSchema'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_schema_id', 'order'], 'integer'],
            [['name', 'color'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks_schema_cat';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tasks_schema_id' => Yii::t('app', 'Schemat'),
            'name' => Yii::t('app', 'Nazwa'),
            'order' => 'Order',
            'color' => Yii::t('app', 'Kolor')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskSchemas()
    {
        return $this->hasMany(\common\models\TaskSchema::className(), ['tasks_schema_cat_id' => 'id'])->orderBy(['order'=>SORT_ASC]);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksSchema()
    {
        return $this->hasOne(\common\models\TasksSchema::className(), ['id' => 'tasks_schema_id']);
    }
}
