<?php

namespace common\models;

use Yii;
use \common\models\base\Message as BaseMessage;

/**
 * This is the model class for table "message".
 */
class Message extends BaseMessage
{
    public function getSource()
    {
        return $this->hasOne(\common\models\SourceMessage::className(), ['id' => 'id']);
    }
}
