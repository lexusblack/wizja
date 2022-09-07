<?php
namespace common\widgets;

use Yii;
use common\models\GearCompany;
use common\widgets\AddModelWidget;
use kartik\form\ActiveForm;
use yii\bootstrap\Html;

/* @var $form ActiveForm */
/* @var $model Location */
class AddCompanyWidget extends AddModelWidget
{

    public function init()
    {
        $this->title = Yii::t('app', 'Dodaj Producenta');
        $this->_targetClassName = GearCompany::className();
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
            foreach ($model->existingModels as $company)
            {
                $link = Html::tag('div',Html::a(Yii::t('app', 'Użyj'), '#', ['class'=>'use-location', 'data'=>['id'=>$company->id]]));
                $content .= Html::tag('div', $$company->displayLabel.$link, ['class'=>'well well-sm']);
            }

            $content .= $form->field($model, 'type')->checkbox([
                'label' => Yii::t('app', 'Dodać mimo to?')
            ]);
        }

        $content .= Html::endTag('div');
        $content .= Html::endTag('div');
        $this->registerScripts();
        return $content;
    }

    protected function registerScripts()
    {
        $this->view->registerJs('
           $(document).on("click", ".use-location", function(e){
                e.preventDefault();
                var el = $(this);
                $("[id$=-gear_company_id]").val(el.data("id")).trigger("change");
                $("#'.$this->widgetId.'").modal("hide");
                return false;
           });
           
        ');
    }


}