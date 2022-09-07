<?php

namespace common\models;

use Yii;
use \common\models\base\EventASResult as BaseEventASResult;

/**
 * This is the model class for table "event_additional_statut_result".
 */
class EventASResult extends BaseEventASResult
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'event_additional_statut_id', 'event_additional_statut_name_id'], 'integer']
        ]);
    }

public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);


                        $eventlog = new EventLog;
                        $eventlog->event_id = $this->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = $this->eventAdditionalStatut->name.Yii::t('app', " zmienił wartość na ").$this->eventAdditionalStatutName->name;
                        $eventlog->save();
    }
	
}
