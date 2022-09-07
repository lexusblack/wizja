<?php

namespace common\models;

use Yii;
use \common\models\base\TaskAttachment as BaseTaskAttachment;

/**
 * This is the model class for table "customer_attachment".
 */
class TaskAttachment extends BaseTaskAttachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/task-attachment/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/task-attachment/'.$this->filename);
    }
	
    public function beforeDelete()
    {
        //dodać komentarz
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {
                //dodać komentarz
                $note = new TaskNote();
                $note->task_id = $this->task_id;
                $note->user_id = Yii::$app->user->id;
                $note->text = Yii::t('app', 'Dodano plik: ').$this->filename;
                $note->save();
            }
         
    }
}
