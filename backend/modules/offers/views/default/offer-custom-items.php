<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\OfferCustomItems */
/* @var $form ActiveForm */

$this->title = Yii::t('app', 'Dodaj pozycję do oferty');
?>
<div class="container">
    <h1><?=$this->title?></h1>
    <div class="offer-custom-items">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 999, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '#add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => new \common\models\OfferCustomItems(),
                'formId' => 'dynamic-form',
                'formFields' => [
                    'quantity',
                    'name',
                    'price',
                    'discount',
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
                <div class="item panel panel-default" data-id="<?=$model->id?>"><!-- widgetBody -->
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left"></h3>
                        <div class="pull-right">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <?= $form->field($model, "[{$i}]quantity")->textInput(['type'=>'hidden', 'class' => 'quantity_input', 'value'=>1, 'min' => 1])->label(false) ?>
                            <?= $form->field($model, "[{$i}]name") ?>
                            <?= $form->field($model, "[{$i}]price")->textInput(['class' => 'form-control price_input']) ?>
                            <?= $form->field($model, "[{$i}]diff_count") ?>
                            <?= $form->field($model, "[{$i}]discount")->textInput(['type'=>'number', 'min' => 0]) ?>
                            <?= $form->field($model, "[{$i}]department_id")->dropDownList(\common\models\Department::getModelList(), ['prompt'=>'']) ?>
                            <?= $form->field($model, "[{$i}]cost") ?>
                        </div>
                       
                    </div><!-- .row -->

                </div>
            
            <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    </div><!-- offer-custom-items -->
</div>

<?php
$this->registerJs('
    $(".price_input").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});


');

$this->registerJs('

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(".quantity_input").val(1);
    console.log("afterInsert");
});

$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});


$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
    if (! confirm("'.Yii::t('app', 'Na pewno chcesz usunąć?').'")) {
        return false;
    }
    return true;
});

$(".dynamicform_wrapper").on("afterDelete", function(e) {
    console.log("Deleted item!");
});

');