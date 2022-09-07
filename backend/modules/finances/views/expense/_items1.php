<?php
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use yii\bootstrap\Html;
use common\models\ExpenseContentRate;

/* @var $this \yii\web\View; */
/* @var $model \common\models\Expense */
/* @var $form \kartik\form\ActiveForm */
/* @var $items \common\models\ExpenseContentRate[] */



?>
<div id="expenses-form-items-rates">
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper_rates', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items-rates', // required: css class selector
    'widgetItem' => '.item-rate', // required: css class
//    'limit' => 50, // the maximum times, an element can be cloned (default 999)
    'min' => 0, // 0 or 1 (default 1)
    'insertButton' => '.add-expense-rate', // css class
    'deleteButton' => '.remove-expense-rate', // css class
    'model' => new ExpenseContentRate(),
    'formId' => 'expenses-form',
    'formFields' => [
        'vat',
        'netto',
        'tax',
        'brutto',
        'description',
    ],
]);
?>

    <div class="container-items-rates"><!-- widgetContainer -->
        <div class="row">
            <div class="col-lg-2"><?php echo  $items[0]->getAttributeLabel('vat'); ?></div>
            <div class="col-lg-2"><?php echo  $items[0]->getAttributeLabel('netto'); ?></div>
            <div class="col-lg-2"><?php echo  $items[0]->getAttributeLabel('tax'); ?></div>
            <div class="col-lg-2"><?php echo  $items[0]->getAttributeLabel('brutto'); ?></div>
            <div class="col-lg-4"></div>


        </div>
        <?php foreach ($items as $i => $item): ?>
            <div class="item-rate"><!-- widgetBody -->
                <?php
                // necessary for update action.
                if ($item->isNewRecord == false) {
                    echo Html::activeHiddenInput($item, "[{$i}]id");
                }
                ?>
                <div class="row">
                    <div class="col-lg-2">
                        <?php //echo  $form->field($item, "[{$i}]vat")->dropDownList(\backend\modules\finances\Module::getVatList())->label(false) ?>
                        <?php echo  $form->field($model, "[{$i}]vat")->textInput()->label(false); ?>
                    </div>
                    <div class="col-lg-2">
                        <?php echo  $form->field($item, "[{$i}]netto")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-2">
                        <?php echo  $form->field($item, "[{$i}]tax")->textInput(['maxlength' => true, 'disabled'=>true])->label(false) ?>
                    </div>
                    <div class="col-lg-2">
                        <?php echo  $form->field($item, "[{$i}]brutto")->textInput(['maxlength' => true, 'disabled'=>true])->label(false) ?>
                    </div>
                    <div class="col-lg-4">
                        <button type="button" class="remove-expense-rate btn btn-sm  btn-danger"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo $form->field($item, "[{$i}]description")->textarea(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clearfix">
        <button type="button" class="add-expense-rate btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i></button>
    </div>





<?php DynamicFormWidget::end(); ?>
</div>