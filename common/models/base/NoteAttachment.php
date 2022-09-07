<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "note_attachment".
 *
 * @property integer $id
 * @property integer $note_id
 * @property string $filename
 * @property string $extension
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\Note $note
 */
class NoteAttachment extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'note'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['note_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'note_attachment';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note_id' => 'Note ID',
            'filename' => 'Filename',
            'extension' => 'Extension',
            'mime_type' => 'Mime Type',
            'base_name' => 'Base Name',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNote()
    {
        return $this->hasOne(\common\models\Note::className(), ['id' => 'note_id']);
    }
        public function getFileUrl()
    {
        return Yii::getAlias('@uploads/note-attachment/'.$this->filename);
    }
    }
