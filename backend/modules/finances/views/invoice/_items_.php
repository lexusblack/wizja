<?php
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use yii\bootstrap\Html;

/* @var $this \yii\web\View; */
/* @var $model \common\models\Invoice */
/* @var $form \kartik\form\ActiveForm */
/* @var $items \common\models\InvoiceContent[] */


?>
<div id="invoice-form-items">
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item-invoice', // required: css class
    'limit' => 50, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-invoice-item', // css class
    'deleteButton' => '.remove-invoice-item', // css class
    'model' => $items[0],
    'formId' => 'invoices-form',
    'formFields' => [
        'item_id',
        'item_class',
        'item_tmp_name',
        'name',
        'classification',
        'unit',
        'count',
        'price',
        'discount_percent',
        'vat'

    ],
]);
?>

    <div class="container-items"><!-- widgetContainer -->
        <div class="row">
            <div class="col-lg-3">
                <?php echo  $items[0]->getAttributeLabel('name');  ?>
            </div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('classification'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('unit'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('count'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('price'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('discount_percent'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('vat'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('netto'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('brutto'); ?></div>
            <div class="col-lg-1"></div>


        </div>
        <?php foreach ($items as $i => $item): ?>
            <div class="item-invoice"><!-- widgetBody -->
                <?php
                $initText = '';
                // necessary for update action.
                if ($item->isNewRecord == false) {

                    $owner = $item->loadOwnerModel();
                    $initText = $owner->name.' ['.$owner->number.']';
                    $item->loadTmpName();
                    echo Html::activeHiddenInput($item, "[{$i}]id");
                }
                ?>
                <div class="row">
                    <div class="col-lg-3">
                        <?php  echo Html::activeHiddenInput($item, "[{$i}]name"); ?>
                        <?php  echo Html::activeHiddenInput($item, "[{$i}]item_class"); ?>
                        <?php  echo Html::activeHiddenInput($item, "[{$i}]item_id"); ?>
                        <?php echo  $form->field($item, "[{$i}]item_tmp_name")->widget(Select2::className(), [
                                'initValueText' => $initText,
                            'pluginOptions' => [
                                'placeholder'=>Yii::t('app', 'Nazwa'),
                                'allowClear' => true,
                                'multiple' => false,
                                'tags' => false,
                                'ajax' => [
                                    'delay' => 50,
                                    'url' => \yii\helpers\Url::to(['/finances/default/list-item']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) {
                                       return {q:params.term};
                                    }')
                                ],
                            ],
                            'pluginEvents' => [
                                'change' => 'function(e){
                                    var data = $(this).select2("data")[0];
                                    var row = $(this).closest(".row");
                                    row.find("[id$=-name]").val(data.text);
                                    row.find("[id$=-item_class]").val(data.item_class);
                                    row.find("[id$=-item_id]").val(data.item_id);
                                    row.find("[id$=-price]").val(data.price);
                                    row.find("[id$=-count]").val(1);
                                }',
                            ],
                        ])
                            ->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]classification")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]unit")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]count")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]price")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]discount_percent")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]vat")->dropDownList(\backend\modules\finances\Module::getVatList())->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]netto")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]brutto")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="remove-invoice-item"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clearfix">
        <button type="button" class="add-invoice-item"><i class="glyphicon glyphicon-plus"></i></button>
    </div>

<?php DynamicFormWidget::end(); ?>
</div>