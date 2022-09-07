<?php

namespace common\models;

use Yii;
use \common\models\base\EventGearOutcomed as BaseEventGearOutcomed;

/**
 * This is the model class for table "event_gear_outcomed".
 */
class EventGearOutcomed extends BaseEventGearOutcomed
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['packlist_id'], 'required'],
            [['packlist_id', 'gear_id', 'quantity', 'event_id'], 'integer']
        ]);
    }
	
}
