<?php

namespace common\models;

use Yii;
use \common\models\base\EventOfferRole as BaseEventOfferRole;

/**
 * This is the model class for table "event_offer_role".
 */
class EventOfferRole extends BaseEventOfferRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'user_role_id', 'quantity'], 'integer'],
            [['schedule'], 'string', 'max' => 255]
        ]);
    }
	
}
