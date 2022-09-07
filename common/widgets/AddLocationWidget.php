<?php
namespace common\widgets;

use Yii;
use common\models\Location;
use common\widgets\AddModelWidget;
use kartik\form\ActiveForm;
use yii\bootstrap\Html;

/* @var $form ActiveForm */
/* @var $model Location */
class AddLocationWidget extends AddModelWidget
{

    protected $permissionName = 'locationLocationsAdd';

    public function init()
    {
        $this->title = Yii::t('app', 'Dodaj miejsce');
        $this->_targetClassName = Location::className();
        parent::init();
    }

    protected function _renderFormFields($form, $model)
    {
        $content = '';
        $content .= Html::beginTag('div', ['class'=>'row']);
        $content .= Html::beginTag('div',  ['class'=>'col-md-6']);
        $content .=  $form->field($model, 'name')->textInput(['maxlength' => true]);
        if ($model->existingModels !== null)
        {
            foreach ($model->existingModels as $location)
            {
                $link = Html::tag('div',Html::a(Yii::t('app', 'Użyj'), '#', ['class'=>'use-location', 'data'=>['id'=>$location->id]]));
                $content .= Html::tag('div', $location->displayLabel.$link, ['class'=>'well well-sm']);
            }

            $content .= $form->field($model, 'type')->checkbox([
                'label' => Yii::t('app', 'Dodać mimo to?')
            ]);
        }
        $content .=  $form->field($model, 'address')->textInput(['maxlength' => true]);
        $content .=  $form->field($model, 'city')->textInput(['maxlength' => true]);





        $content .= Html::endTag('div');
        $content .= Html::beginTag('div',  ['class'=>'col-md-6']);
        $content .=  $form->field($model, 'zip')->textInput(['maxlength' => true]);
        $content .=  $form->field($model, 'country')->textInput(['maxlength' => true]);
        $content .=  $form->field($model, 'info')->textarea(['rows' => 6]);

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
                $("[id$=-location_id]").val(el.data("id")).trigger("change");
                $("#'.$this->widgetId.'").modal("hide");
                return false;
           });
           
        ');
    }


}