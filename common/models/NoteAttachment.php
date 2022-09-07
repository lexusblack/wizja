<?php

namespace common\models;

use Yii;
use \common\models\base\NoteAttachment as BaseNoteAttachment;

/**
 * This is the model class for table "note_attachment".
 */
class NoteAttachment extends BaseNoteAttachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['note_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }
	
}
