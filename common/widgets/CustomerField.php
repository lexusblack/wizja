<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class CustomerField extends Select2
{
    public $customer = null;
    public $supplier = null;

    public function init()
    {

        $this->data = \common\models\Customer::getList();
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'tags' => false,
            'ajax' => [
                'delay' => 50,
                'url' => \yii\helpers\Url::to(['/customer/list', 'supplier'=>$this->supplier, 'customer'=>$this->customer]),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                 
                       return {q:params.term}; 
                    }')
            ],
        ];
        $this->pluginEvents = [
            'change' => 'function(e) {
                var $val = $(this).val();
                
                var $contactField = $("#'.Html::getInputId($this->model, 'contact_id').'"); 
                if ($val == "")
                {
                    $contactField.prop("disabled", true).val("").trigger("change");
                }
                else
                {
                    $contactField.prop("disabled", false).val("").trigger("change");
                }
                
            }',
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
                    $.post("'.\yii\helpers\Url::to(['/customer/create']).'", {"Customer[name]":value}, function(result)
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
        echo AddCustomerWidget::widget();
    }
}