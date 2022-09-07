<?php
namespace backend\actions;

class UploadMultipleAction extends UploadAction
{

    public $targetClassName;

    public function init()
    {
        $this->afterUploadHandler = [$this, 'batchCreate'];
        parent::init();
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];

        $model = \Yii::createObject($this->targetClassName);
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }
}