<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_provision".
 *
 * @property integer $id
 * @property integer $event_id
 * @property string $section
 * @property integer $type
 * @property string $value
 *
 * @property \common\models\Event $event
 */
class EventProvision extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_provision';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'section' => 'Section',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
    }
