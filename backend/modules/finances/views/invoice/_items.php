<?php
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use yii\bootstrap\Html;
use common\models\InvoiceContent;
use yii\helpers\ArrayHelper;

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
    'model' => new InvoiceContent(),
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

$this->registerJs('
$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    var el = $(".item-invoice:last");
    el.find(".item-name-list").val("");
    el.find("[id$=-name]").closest(".form-group").hide();
});
');
?>

    <div class="container-items"><!-- widgetContainer -->
        <div class="row">
            <div class="col-lg-3 white">
                <?php echo  $items[0]->getAttributeLabel('name');  ?>
            </div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('classification'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('unit'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('count'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('price'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('discount_percent'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('vat'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('netto'); ?></div>
            <div class="col-lg-1 white"><?php echo  $items[0]->getAttributeLabel('brutto'); ?></div>
            <div class="col-lg-1 white"></div>


        </div>
        <?php foreach ($items as $i => $item): ?>
            <div class="item-invoice"><!-- widgetBody -->
                <?php
                // necessary for update action.
                $initialValueText = '';
                if ($item->isNewRecord == false) {
                    $owner = $item->loadOwnerModel();
                    if ($owner !== null)
                    {
                        $initialValueText = $owner->name;
                        $item->loadTmpName();
                    }


                    echo Html::activeHiddenInput($item, "[{$i}]id");
                }
                ?>
                <div class="row">
                    <div class="col-lg-3">
                        <?php  echo Html::activeHiddenInput($item, "[{$i}]item_class"); ?>
                        <?php  echo Html::activeHiddenInput($item, "[{$i}]item_id"); ?>
                        <div class="row name-row">
                            <div class="col-lg-10">

                                <?php //echo  $form->field($item, "[{$i}]item_tmp_name")->dropDownList(\backend\modules\finances\Module::listItems(), ['prompt'=> Yii::t('app', 'Wybierz...'), 'class'=>'item-name-list form-control'])->label(false) ?>
                                <?php echo $form->field($item, "[{$i}]item_tmp_name")->widget(\kartik\widgets\Select2::className(), [
                                        'data' => ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'id', 'name'),
                                        'options' => [
                                            'placeholder' => Yii::t('app', 'Wybierz...'),
                                            'class'=>'item-name-list form-control'
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'multiple' => false,
                                        ],
                                    ])->label(false);
                                ?>

                                <?php echo  $form->field($item, "[{$i}]name")->textInput(['placeholder'=> Yii::t('app', 'Wpisz...')])->label(false) ?>

                            </div>
                            <div class="col-lg-2">
                                <?php echo Html::a(Html::icon('pencil'), '#', ['class'=>'btn btn-primary name-toggle']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]classification")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]unit")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]count")->textInput(['maxlength' => true, 'class'=>'form-control item-calculate item-count'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]price")->textInput(['maxlength' => true, 'class'=>'form-control item-calculate item-price'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]discount_percent")->textInput(['maxlength' => true, 'class'=>'form-control item-calculate item-discount'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]vat")->dropDownList(\backend\modules\finances\Module::getVatList(), ['class'=>'form-control item-calculate item-vat'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]netto")->textInput(['maxlength' => true, 'disabled'=>true, 'class'=>'form-control item-calculate item-netto'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]brutto")->textInput(['maxlength' => true, 'disabled'=>true, 'class'=>'form-control item-calculate item-brutto'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="remove-invoice-item btn btn-danger"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clearfix">
        <button type="button" class="add-invoice-item btn btn-success"><i class="glyphicon glyphicon-plus"></i></button>
    </div>





<?php DynamicFormWidget::end(); ?>
</div>

<?php
$detailUrl = \common\helpers\Url::to(['default/item-data']);
$this->registerJs('

 $(document).on("change",".item-name-list", function(e){
    var el = $(this);
    el.prop("disabled", true);
    $.get("'.$detailUrl.'", {id:el.val()}, function(data){
//        console.log(data);
        var row = el.closest(".item-invoice");
        row.find("[id$=-name]").val(data.text);
        row.find("[id$=-item_class]").val(data.item_class);
        row.find("[id$=-item_id]").val(data.item_id);
        row.find("[id$=-price]").val(data.price);
        row.find("[id$=-count]").val(1);
        row.find("[id$=-discount_percent]").val(0);
        el.prop("disabled", false);
        summary();
    });
});
        
 
summary();
function summary()
{
    var nettoSum = 0;
    var taxSum = 0;
    var bruttoSum = 0;
    
    $(".item-invoice").each(function(i,element){
        var el = $(element);
        var discount = el.find(".item-discount").val();
        var price = el.find(".item-price").val();
        var count = el.find(".item-count").val();
        var value = price * count;
        var netto = value - (value *(discount/100));
        var vat = el.find(".item-vat").val()/100;
        var tax = netto * vat;
        var brutto = netto * (1 + vat);
        
        el.find(".item-netto").val(netto);
        el.find(".item-brutto").val(brutto);
        
        nettoSum += netto;
        taxSum += tax;
        bruttoSum += brutto;
    });
    
    $(".summary-netto").html(nettoSum.toFixed(2));
    $(".summary-brutto").html(bruttoSum.toFixed(2));
    $(".summary-tax").html(taxSum.toFixed(2));
}

namesToggle();
function namesToggle()
{
    $(".item-invoice").each(function(i,element){
    
        
        var row = $(this);
        /*
        if(!row.find("[id$=-name]").val())
        {
            row.find("[id$=-item_tmp_name]").closest(".form-group").show();
             row.find("[id$=-name]").closest(".form-group").hide();
        }
        else
        {
             row.find("[id$=-item_tmp_name]").closest(".form-group").hide();
             row.find("[id$=-name]").closest(".form-group").show();
             
        }
        */
        row.find("[id$=-item_tmp_name]").closest(".form-group").hide();
             row.find("[id$=-name]").closest(".form-group").show();
    });
    
}

$(document).on("input", ".item-calculate", function(e){
$(e.target).val($(e.target).val().replace(",", "."));
    $(e.target).val($(e.target).val().replace(" ", ""));
 summary();});
$(document).on("change", ".item-calculate", summary);
$(document).on("click", ".name-row .name-toggle", toggleName);
function toggleName(e)
{
    e.preventDefault();
    var row = $(this).closest(".item-invoice");
    
    var list =row.find("[id$=-item_tmp_name]");
    var text = row.find("[id$=-name]");
    var itemId = row.find("[id$=-item_id]");
    if (row.find("[id$=-item_tmp_name]").is(":visible"))
    {
        list.closest(".form-group").hide();
        list.val("");
        itemId.val("")
        text.closest(".form-group").show();
    }
    else
    {
         list.closest(".form-group").show();
         text.closest(".form-group").hide();
         text.val("");
    }
    
    
}
');

$this->registerCss('
.item-calculate.item-price { min-width: 110px; }
');