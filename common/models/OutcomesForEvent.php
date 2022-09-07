<?php

namespace common\models;

use common\models\Event;
use Yii;

/**
 * This is the model class for table "outcomes_for_event".
 *
 * @property integer $id
 * @property integer $outcome_id
 * @property integer $event_id
 *
 * @property OutcomesWarehouse $outcome
 * @property Event $event
 */
class OutcomesForEvent extends \common\models\base\OutcomesForEvent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_for_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outcome_id', 'event_id'], 'required'],
            [['outcome_id', 'event_id'], 'integer'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutcomesWarehouse::className(), 'targetAttribute' => ['outcome_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'outcome_id' => Yii::t('app', 'ID wydania z magazynu'),
            'event_id' => Yii::t('app', 'ID wydarzenia'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcome()
    {
        return $this->hasOne(OutcomesWarehouse::className(), ['id' => 'outcome_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }

    public function afterSave($insert, $changeAttributes)
    {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $this->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Wydano sprzęt. Wydanie nr ").$this->outcome_id;
                        $eventlog->save();                        
    }
}
