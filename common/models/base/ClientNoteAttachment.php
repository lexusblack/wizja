<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "client_note_attachment".
 *
 * @property integer $id
 * @property integer $client_note_id
 * @property string $filename
 * @property string $extension
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\CustomerNote $clientNote
 */
class ClientNoteAttachment extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'clientNote'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_note_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_note_attachment';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_note_id' => 'Client Note ID',
            'filename' => 'Filename',
            'extension' => 'Extension',
            'mime_type' => 'Mime Type',
            'base_name' => 'Base Name',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientNote()
    {
        return $this->hasOne(\common\models\CustomerNote::className(), ['id' => 'client_note_id']);
    }
    
    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/note-attachment/'.$this->filename);
    }
}
