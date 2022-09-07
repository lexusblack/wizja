<?php
namespace common\widgets;

use Yii;
use common\models\LocationType;
use common\widgets\AddModelWidget;
use kartik\form\ActiveForm;
use yii\bootstrap\Html;

/* @var $form ActiveForm */
/* @var $model Location */
class AddLocationTypeWidget extends AddModelWidget
{

    public function init()
    {
        $this->title = Yii::t('app', 'Dodaj typ obiektu');
        $this->_targetClassName = LocationType::className();
        parent::init();
    }

    protected function _renderFormFields($form, $model)
    {
        $content = '';
        $content .= Html::beginTag('div', ['class'=>'row']);
        $content .= Html::beginTag('div',  ['class'=>'col-md-12']);
        $content .=  $form->field($model, 'name')->textInput(['maxlength' => true]);        

    



        $content .= Html::endTag('div');
        $content .= Html::endTag('div');
        $this->registerScripts();
        echo $content;
    }

    protected function registerScripts()
    {
        $this->view->registerJs('
           $(document).on("click", ".use-location", function(e){
                e.preventDefault();
                var el = $(this);
                $("[id$=-location_type_id]").val(el.data("id")).trigger("change");
                $("#'.$this->widgetId.'").modal("hide");
                return false;
           });
           
        ');
    }


}