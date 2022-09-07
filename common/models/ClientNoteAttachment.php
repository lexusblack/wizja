<?php

namespace common\models;

use Yii;
use \common\models\base\ClientNoteAttachment as BaseClientNoteAttachment;

/**
 * This is the model class for table "client_note_attachment".
 */
class ClientNoteAttachment extends BaseClientNoteAttachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['client_note_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }
	
}
