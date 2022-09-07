<?php
namespace common\widgets;

use kartik\select2\Select2;
use Yii;
use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class SkillUserField extends Select2
{
    public function init()
    {

        $this->data = \common\models\Skill::getList();
        $this->options = [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ];
        $this->disabled = empty($this->model->user_id) ? true : false;
        $this->pluginOptions = [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
            'ajax' => [
                'delay' => 50,
                'url' => \yii\helpers\Url::to(['/skill/list']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                       return {user_id:$(this).closest(".form-group").prev(".user_select_box").find("select").val()}; 
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