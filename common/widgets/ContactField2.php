<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class ContactField2 extends Select2
{
    public function init()
    {
        $model = $this->model;
        //$this->initValueText = empty($model->contact_id) ? "" : $model->contact->displayLabel;
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->disabled = empty($model->customer_id) ? true : false;
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => true,
            'tags' => true,
            'ajax' => [
                'delay' => 100,
                'url' => \yii\helpers\Url::to(['/contact/list']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                 
                       return {q:params.term, id:$("#'.Html::getInputId($model, 'customer_id').'").val()}; 
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
                        /*var customer_id = $("#'.Html::getInputId($model, 'customer_id').'").val();
                        $.post("'.\yii\helpers\Url::to(['/contact/create']).'", {"Contact[last_name]":value, "Contact[customer_id]":customer_id}, function(result)
                        {
                            el2.prop("value", result.id);
                            el.val(result.id);
                            el.trigger("change");
                            
                        });*/
                        return false;
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