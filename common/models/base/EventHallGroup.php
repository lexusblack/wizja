<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_hall_group".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $hall_group_id
 * @property string $start_time
 * @property string $end_time
 *
 * @property \common\models\Event $event
 * @property \common\models\HallGroup $hallGroup
 */
class EventHallGroup extends \yii\db\ActiveRecord
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
            'hallGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'hall_group_id', 'statut_id'], 'integer'],
            [['start_time', 'end_time', 'description'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_hall_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'hall_group_id' => 'Hall Group ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'statut_id'=>Yii::t('app', 'Status rezerwacji')
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
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatut()
    {
        return $this->hasOne(\common\models\HallGroupStatut::className(), ['id' => 'statut_id']);
    }
    }
