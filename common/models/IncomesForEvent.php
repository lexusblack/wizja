<?php

namespace common\models;

use common\models\Event;
use Yii;

/**
 * This is the model class for table "incomes_for_event".
 *
 * @property integer $id
 * @property integer $income_id
 * @property integer $event_id
 *
 * @property IncomesWarehouse $income
 * @property Event $event
 */
class IncomesForEvent extends \common\models\base\IncomesForEvent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incomes_for_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['income_id', 'event_id'], 'required'],
            [['income_id', 'event_id'], 'integer'],
            [['income_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomesWarehouse::className(), 'targetAttribute' => ['income_id' => 'id']],
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
            'income_id' => Yii::t('app', 'ID przyjęcia'),
            'event_id' => Yii::t('app', 'ID wydarzenia'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncome()
    {
        return $this->hasOne(IncomesWarehouse::className(), ['id' => 'income_id']);
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
                        $eventlog->content = Yii::t('app', "Przyjęto sprzęt. Przyjęcie nr ").$this->income_id;
                        $eventlog->save();                        
    }
}
