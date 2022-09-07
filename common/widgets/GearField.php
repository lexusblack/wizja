<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class GearField extends Select2
{
    public function init()
    {
        $model = $this->model;
        $this->initValueText = empty($model->gear_id) ? "" : $model->gear_id->displayLabel;
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->disabled = empty($model->category_id) ? true : false;
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
            'ajax' => [
                'delay' => 100,
                'url' => \yii\helpers\Url::to(['/gear/list']),
                'data' => new \yii\web\JsExpression('function(params) {
                       return {id:$("#w1").val()}; 

                    }')
            ],
        ];

        parent::init();
    }

    public function run()
    {
        parent::run();
    }
}