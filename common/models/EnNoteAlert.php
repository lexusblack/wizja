<?php

namespace common\models;

use Yii;
use \common\models\base\EnNoteAlert as BaseEnNoteAlert;

/**
 * This is the model class for table "en_note_alert".
 */
class EnNoteAlert extends BaseEnNoteAlert
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'en_note_id'], 'integer']
        ]);
    }
	
}
