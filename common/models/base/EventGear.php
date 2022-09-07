<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\helpers\ArrayHelper;

/**
 * This is the base model class for table "event_gear".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $gear_id
 * @property integer $quantity
 * @property string $start_time
 * @property string $end_time
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\Gear $gear
 * @property \common\models\Event $event
 */
class EventGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gear',
            'event'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'gear_id', 'quantity', 'type'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'gear_id' => 'Gear ID',
            'quantity' => 'Quantity',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'type' => 'Type',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
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
    public function getPacklistGears()
    {
        return $this->hasMany(\common\models\PacklistGear::className(), ['event_gear_id' => 'id']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}
