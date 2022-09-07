<?php
namespace common\components\grid;

use yii\grid\DataColumn;
use yii\bootstrap\Html;

class PhotoColumn extends DataColumn
{
    public $width = '100px';
    public function init()
    {
        $this->contentOptions=['class'=>'text-center'];
        $this->attribute = 'photo';
        $this->value = function ($model, $key, $index, $column) {
            if ($model->photo == null)
            {
                return '-';
            }
            return Html::img($model->getPhotoUrl(), ['style'=>'width:'.$this->width]);
        };
        $this->format = 'raw';
        $this->filter = false;
        parent::init();
    }
}