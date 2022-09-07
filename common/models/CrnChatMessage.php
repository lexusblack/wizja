<?php

namespace common\models;

use Yii;
use \common\models\base\CrnChatMessage as BaseCrnChatMessage;

/**
 * This is the model class for table "crn_chat_message".
 */
class CrnChatMessage extends BaseCrnChatMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['crn_chat_id', 'user_id', 'read'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['company'], 'string', 'max' => 45],
            [['user'], 'string', 'max' => 255]
        ]);
    }

        public function getTime()
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);

        $originalDate = $this->datetime;
        $week = [0=>'nd', 1=>'pon.', 2=>'wt.', 3=>'śr.', 4=>'czw.', 5=>'pt.', 6=>'sb.'];
        if (date('Y-m-d') == date("Y-m-d", strtotime($originalDate)))
             $newDate = date("H:i", strtotime($originalDate));
         else
            $newDate = $week[date("w", strtotime($originalDate))];
       
        return $newDate;
    }
	
}
