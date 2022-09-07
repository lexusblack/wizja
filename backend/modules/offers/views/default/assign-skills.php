<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\widgets\DateTimePicker;


/* @var $this yii\web\View */
/* @var $model common\models\Offer */

$this->title = Yii::t('app', 'Dodaj Umiejętności');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 999, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '#add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => new \common\models\OfferUserSkills(),
        'formId' => 'dynamic-form',
        'formFields' => [
            'user_id',
            'skill_id',
            'time_from',
            'time_to',
        ],
    ]); ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="pull-right">
                <button type="button" id="add-item" class="add-item btn btn-success"><i class="glyphicon glyphicon-plus"></i></button>
            </div>
        </div>
    </div>
    
    <hr>

    <div class="container-items"><!-- widgetContainer -->
    <?php foreach ($models as $i => $model): ?>
        <div class="item panel panel-default"><!-- widgetBody -->
            <div class="panel-heading">
                <h3 class="panel-title pull-left"></h3>
                <div class="pull-right">
                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="user_select_box">
                        <?= $form->field($model, "[{$i}]user_id")->widget(\common\widgets\UserSkillField::className(), []);?>
                    </div>
                    <?= $form->field($model, "[{$i}]skill_id")->widget(\common\widgets\SkillUserField::className(), []);?>

                    <?= $form->field($model, "[{$i}]time_from")->widget(DateTimePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app', 'Wprowadź czas wydarzenia...')],
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]);?>
                    <?= $form->field($model, "[{$i}]time_to")->widget(DateTimePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app', 'Wprowadź czas wydarzenia...')],
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]);?>
                </div>
               
            </div><!-- .row -->

        </div>
    
    <?php endforeach; ?>
    </div>
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('$("body").on("change",".user_select_box select",function(){

    var val = $(this).val();

    var skillField = $(this).closest(".user_select_box").next(".form-group").find("select"); 
    if (val == "")
        {
            skillField.prop("disabled", true).val("").trigger("change");
        }
        else
        {
            skillField.prop("disabled", false).val("").trigger("change");
        }

        
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        var datePickers = $(this).find("[data-krajee-datetimepicker]");
        datePickers.each(function(index, el) {
            $(this).parent().removeData().datetimepicker("remove");
            $(this).parent().datetimepicker(eval($(this).attr("data-krajee-kvdatetimepicker")));
        });
    });');
