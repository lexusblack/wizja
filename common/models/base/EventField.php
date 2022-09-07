<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_field".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $event_field_setting_id
 * @property string $value_int
 * @property string $value_text
 *
 * @property \common\models\Event $event
 * @property \common\models\EventFieldSetting $eventFieldSetting
 */
class EventField extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event',
            'eventFieldSetting'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'event_field_setting_id'], 'integer'],
            [['value_int'], 'number'],
            [['value_text'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_field';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'event_field_setting_id' => 'Event Field Setting ID',
            'value_int' => 'Value Int',
            'value_text' => 'Value Text',
        ];
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
    public function getEventFieldSetting()
    {
        return $this->hasOne(\common\models\EventFieldSetting::className(), ['id' => 'event_field_setting_id']);
    }
    }
