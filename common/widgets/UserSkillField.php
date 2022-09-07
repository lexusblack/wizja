<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class UserSkillField extends Select2
{
    public function init()
    {

        $this->data = \common\models\User::getList();
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
            'ajax' => [
                'delay' => 50,
                'url' => \yii\helpers\Url::to(['/user/list']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {                
                       return {id:$(this).val()}; 
                    }')
            ],
        ];
        $this->pluginEvents = [
            'change' => 'function(e) {
                // var val = $(this).val();

                // console.log(val);
                
                // var skillField = $(this).closest(".form-group").next(".form-group").find("select"); 
                // if (val == "")
                // {
                //     skillField.prop("disabled", true).val("").trigger("change");
                // }
                // else
                // {
                //     skillField.prop("disabled", false).val("").trigger("change");
                // }
                
            }',
        ];
        parent::init();
    }

    public function run()
    {
        parent::run();
    }
}