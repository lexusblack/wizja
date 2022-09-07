<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;

class LocationField extends Select2
{
    public function init()
    {

        $this->data = \common\models\Location::getList();
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'ajax' => [
                'delay' => 50,
                'url' => \yii\helpers\Url::to(['/location/list']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                       return {q:params.term}; 
                }')
            ],
        ];
        $this->pluginEvents = [

            'select2:selecting'=>'function(e){
                var el = $(e.target);
                var el2 = el.find("option[data-select2-tag]").last();
                var value = el2.val();
                if (value==undefined)
                {
                    return true;
                }
                else
                {
                    $.post("'.\yii\helpers\Url::to(['/location/create']).'", {"Location[name]":value}, function(result)
                    {
                        el2.prop("value", result.id);
                        el.val(result.id);
                        el.trigger("change");
                        
                    });
                    return true;
                }
            }',
        ];
        parent::init();
    }

    public function run()
    {
        parent::run();
        return AddLocationWidget::widget();
    }
}