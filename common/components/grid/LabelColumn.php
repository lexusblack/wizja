<?php
namespace common\components\grid;

use common\helpers\StringHelper;
use kartik\grid\DataColumn;
use yii\bootstrap\Html;
use yii\helpers\Inflector;

class LabelColumn extends DataColumn
{
    public function init()
    {
        $this->contentOptions=['class'=>'text-center'];
        $this->pageSummary = false;
        $this->value = function ($model, $key, $index, $column) {
            $methodName = 'get'.Inflector::camelize($this->attribute).'Label';
            if (method_exists($model, $methodName))
            {
                return call_user_func_array([$model, $methodName], []);
            }
            else
            {
                return '-';
            }
            return Html::img($model->getPhotoUrl(), ['style'=>'width:'.$this->width]);
        };
        parent::init();
    }
}