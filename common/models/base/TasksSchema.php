<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "tasks_schema".
 *
 * @property integer $id
 * @property string $name
 * @property integer $default
 * @property integer $type
 *
 * @property \common\models\TasksSchemaCat[] $tasksSchemaCats
 */
class TasksSchema extends \yii\db\ActiveRecord
{

    const PROJECT = 1;
    const EVENT = 2;
    const RENTAL = 3;
    const MEETING = 4;
    const SERVICE = 5;

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'tasksSchemaCats'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default', 'type', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks_schema';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'default' => Yii::t('app', 'Domyślny'),
            'type' => Yii::t('app', 'Typ'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksSchemaCats()
    {
        return $this->hasMany(\common\models\TasksSchemaCat::className(), ['tasks_schema_id' => 'id'])->orderBy(['order'=>SORT_ASC]);
    }
    

    public function getSchemaTypes()
    {
        $types = [
            static::PROJECT => Yii::t('app', 'Projekt'),
            static::EVENT => Yii::t('app', 'Wydarzenie'),
            static::RENTAL => Yii::t('app', 'Wypożyczenie'),
            static::MEETING => Yii::t('app', 'Spotkanie'),
            static::SERVICE => Yii::t('app', 'Serwis')
        ];
        return $types;
    }

    public static function getList($type)
    {
        $query = self::find()->orderBy('name ASC');
        $schemaType = 0;
        if ($type=='event')
            $schemaType = static::EVENT;
        if ($type=='rent')
            $schemaType = static::RENTAL;
        if ($type=='project')
            $schemaType = static::PROJECT;
        if ($schemaType)
            $query->andWhere([
                'type' => $schemaType
            ]);
            $query->andWhere([
                'active' => 1
            ]);
        $list = [];

        $models = $query->all();
        foreach ($models as $model) {
            $list[$model->id] = $model->name;
            if ($model->default)
                $list[$model->id] .= Yii::t('app', ' (domyślny)');

        }

        return $list;
    }
}
