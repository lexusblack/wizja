<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use common\models\Event;
use yii\bootstrap\Html;
use yii\widgets\Pjax;
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
?>

<div id="project-status-section">
<?= $form->field($model, 'offer_prepared')->checkbox(['disabled'=>true]); ?>
<?= $form->field($model, 'offer_sent')->checkbox(['disabled' => !$user->can('eventsEventEditEyeFinanceProjectStatusEdit')])->hint($model->getOfferSentHint()) ?>
<?= $form->field($model, 'offer_accepted')->checkbox(['disabled'=>true])->hint($model->getOfferAcceptedHint()); ?>

<?= $form->field($model, 'ready_to_invoice')->checkbox(['disabled' => !$user->can('eventsEventEditEyeFinanceProjectStatusEdit')])->hint($model->getReadyToInvoiceHint()) ?>
<?= $form->field($model, 'expense_entered')->checkbox(['disabled' => !$user->can('eventsEventEditEyeFinanceProjectStatusEdit')])->hint($model->getExpenseEnteredHint()) ?>

<?= $form->field($model, 'invoice_issued')->dropDownList(Event::invoiceValueList(), ['disabled'=>true]) ?>
<?= $form->field($model, 'expense_status')->checkbox(['disabled'=>true]); ?>
<?= $form->field($model, 'project_settled')->checkbox(['disabled'=>true]); ?>
<?= $form->field($model, 'project_paid')->dropDownList(Event::projectPaidList(), ['disabled'=>true]) ?>

<?= $form->field($model, 'expenses_paid')->checkbox(['disabled'=>true]); ?>
<?= $form->field($model, 'project_done')->checkbox(['disabled'=>true]); ?>

<?php
//???: Co z tymi?
//$form->field($model, 'invoice_sent')->checkbox()
//echo $form->field($model, 'transfer_booked')->checkbox() ?>

<?php echo $form->field($model, 'invoice_number')->textInput(['maxlength' => true, 'disabled'=>$model->invoice?true:false]) ?>

</div>

<?php
$this->registerJs('
$(document).on("change", "#project-status-section :checkbox", function(e){
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    $.post(form.prop("action"), data, function(){
         
       $.get("",{}, function(r) {
            var containerId = "#project-status-section";
            var c = $(r).find(containerId).html();
            $(containerId).html(c);
       });
        
    });
    return false;
});


');
