<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class ProvinceField extends Select2
{
    public function init()
    {
        $model = $this->model;
        $this->initValueText = empty($model->province_id) ? "" : $model->province->name;
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->disabled = empty($model->country_id) ? true : false;
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
            'ajax' => [
                'delay' => 100,
                'url' => \yii\helpers\Url::to(['/province/list']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                 
                       return {q:params.term, id:$("#'.Html::getInputId($model, 'country_id').'").val()}; 
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
                        var country_id = $("#'.Html::getInputId($model, 'country_id').'").val();
                        $.post("'.\yii\helpers\Url::to(['/province/create']).'", {"Province[name]":value, "Province[country_id]":country_id}, function(result)
                        {
                            el2.prop("value", result.id);
                            el.val(result.id);
                            el.trigger("change");
                            
                        });
                        return true;
                    }
                }'
        ];

        parent::init();
    }

    public function run()
    {
        parent::run();
        echo AddContactWidget::widget([
            'owner'=>$this->model,
        ]);
    }
}