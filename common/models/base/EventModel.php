<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_model".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 * @property integer $type
 */
class EventModel extends \yii\db\ActiveRecord
{


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
            [['active', 'type', 'schedule_type_id'], 'integer'],
            [['name', 'color', 'color_line'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_model';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'active' => 'Active',
            'type' => Yii::t('app', 'Sposób wyświetlania podglądu'),
        ];
    }
}
