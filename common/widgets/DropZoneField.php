<?php
namespace common\widgets;

use common\helpers\ArrayHelper;
use common\helpers\Url;
use yii\bootstrap\Html;
use devgroup\dropzone\DropZone;

class DropZoneField extends DropZone
{
    public function init()
    {
        $model = $this->model;

        if ($this->url === null)
        {
            $this->url = ['upload'];
        }
        $this->url = Url::to($this->url);

        if ($this->name === null)
        {
            $this->name = 'file';
        }
        $this->eventHandlers = ArrayHelper::merge([
            'success' => 'function(file, response) {
                       $("#'.Html::getInputId($model, 'filename').'").val(response.filename);
                       $("#'.Html::getInputId($model, 'mime_type').'").val(response.type);
                       $("#'.Html::getInputId($model, 'extension').'").val(response.extension);
                       $("#'.Html::getInputId($model, 'base_name').'").val(response.name);
        
                    }'
        ], $this->eventHandlers);
        parent::init();
    }
}