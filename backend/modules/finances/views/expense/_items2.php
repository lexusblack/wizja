<?php
use common\helpers\Url;
use common\models\Expense;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Html;
use common\models\ExpenseContent;
/* @var $this \yii\web\View; */
/* @var $model \common\models\Expense */
/* @var $form \kartik\form\ActiveForm */
/* @var $items \common\models\ExpenseContent[] */



?>
<div class="ibox">
<div class="ibox-title">
<h4><?=Yii::t('app', 'Pozycje na fakturze')?></h4>
</div>
<div class="ibox-content">
<div id="expenses-form-items">
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item-expense', // required: css class
    'limit' => 50, // the maximum times, an element can be cloned (default 999)
    'min' => 0, // 0 or 1 (default 1)
    'insertButton' => '.add-expense-item', // css class
    'deleteButton' => '.remove-expense-item', // css class
    'model' => $items[0],
    'formId' => 'expenses-form',
    'formFields' => [
        'event_expense_id',
        'name',
        'classification',
        'unit',
        'count',
        'price',
        'discount_percent',
        'vat',
        'netto',
        'brutto',
        'description',
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
            <?php if ($model->type == Expense::TYPE_VAT): ?>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('vat'); ?></div>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('netto'); ?></div>
            <?php endif; ?>
            <div class="col-lg-1"><?php echo  $items[0]->getAttributeLabel('brutto'); ?></div>
            <div class="col-lg-1"></div>


        </div>
        <?php foreach ($items as $i => $item){ ?>
            <div class="item-expense">

                <?php
                echo Html::activeHiddenInput($item, "[{$i}]event_expense_id", ['class'=>'item-event_expense_id']);

                if ($item->isNewRecord == false) {
                    echo Html::activeHiddenInput($item, "[{$i}]id");
                }
                ?>
                <div class="row">
                    <div class="col-lg-3">
                        <?php echo  $form->field($item, "[{$i}]name")->textInput(['maxlength' => true, 'class'=>'form-control item-name'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]classification")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]unit")->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]count")->textInput(['maxlength' => true, 'class'=>'form-control item-count item-calculate'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]price")->textInput(['maxlength' => true, 'class'=>'form-control item-price item-calculate'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]discount_percent")->textInput(['maxlength' => true, 'class'=>'form-control item-calculate item-discount'])->label(false) ?>
                    </div>
                <?php if ($model->type == Expense::TYPE_VAT): ?>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]vat")->dropDownList(\backend\modules\finances\Module::getVatList2(),['class'=>'form-control item-vat item-calculate'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]netto")->textInput(['maxlength' => true, 'disabled'=>true, 'class'=>'form-control item-netto'])->label(false) ?>
                    </div>
                <?php endif; ?>
                    <div class="col-lg-1">
                        <?php echo  $form->field($item, "[{$i}]brutto")->textInput(['maxlength' => true, 'disabled'=>true, 'class'=>'form-control item-brutto'])->label(false) ?>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="remove-expense-item btn btn-sm btn-danger"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo $form->field($item, "[{$i}]description")->textarea(); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="clearfix">
        <button type="button" class="add-expense-item btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i></button>
    </div>





<?php DynamicFormWidget::end(); ?>
</div>
</div>
</div>
<div class="ibox">
<div class="ibox-title yellow-bg">
<h4><?=Yii::t('app', 'Pozycje z kosztów dodatkowych')?></h4>
</div>
<div class="ibox-content">
<div id="event-expenses">
    <table class="table table-condensed table-bordered table-striped">
        <tr>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Kwota netto') ?></th>
            <th><?= Yii::t('app', 'Kwota dla klienta') ?></th>
            <th><?= Yii::t('app', 'Dostawca') ?></th>
            <th></th>
        </tr>
        <tr id="prototype-row" style="display: none">
            <td class="name"></td>
            <td class="netto"></td>
            <td class="amount_customer"></td>
            <td class="customer"></td>
            <td class="text-right"><?php echo Html::a(Html::icon('plus'), '#', ['class'=>'btn btn-xs btn-default expense-add']); ?></td>
        </tr>

    </table>
</div>
</div>
</div>
<?php
$url = Url::to(['event-expenses']);
$this->registerJs('

loadEventExpenses();
function loadEventExpenses()
{
    $("#event-expenses").hide();
    $(".event-expense-row").remove();
    
    var eventId = $("#expense-eventids").val();
    $.post("'.$url.'?customer_id="+$("#expense-customer_id").val(), {id: eventId}, function(response){
        $("#event-expenses").show();
        
        for (i=0; i<response.length; i++)
        {
            var obj = response[i];
           
            var parent = $("#prototype-row").parent();
            var row = $("#prototype-row").clone();
            row.addClass("event-expense-row");
            row.removeAttr("id",null);
            row.appendTo(parent);
            row.show();
            row.find(".name").html(obj.name);
            row.find(".netto").html(obj.amount);
            row.find(".amount_customer").html(obj.amount_customer);
            row.data(obj);
            row.attr("data-eid", obj.id);
            
            if (obj.customer !== null) {
                row.find(".customer").html(obj.customer.name);
            }
        }
        
    });
}

$(document).on("click", ".expense-add", function(e){
    e.preventDefault();
    var row = $(e.currentTarget).closest(".event-expense-row");
    $(".add-expense-item").trigger("click");

    setTimeout(function(){
        var data = row.data();
        var item = $(".item-expense").last();
        item.find(".remove-expense-item").data("expenseid", data.id);
        item.find(".item-event_expense_id").val(data.id);
        item.find(".item-name").val(data.name);
        item.find(".item-price").val(data.amount);
        item.find(".item-count").val(1);
        calculateRowValues(item);
        row.hide();
            $(".item-price").change(function()
    {
        $(this).val($(this).val().replace(",", "."));
        $(this).val($(this).val().replace(" ", ""));
    }
    );
    }, 300);
});
$(document).on("click", ".remove-expense-item", function(e){
    var row = $(e.currentTarget).closest(".item-expense");
    var id =row.find(".item-event_expense_id").val();
    $(".event-expense-row[data-eid="+id+"]").show();

});

$(document).on("input", ".item-calculate", function(e){
    var item = $(e.target).closest(".item-expense");
    
    $(e.target).val($(e.target).val().replace(",", "."));
    $(e.target).val($(e.target).val().replace(" ", ""));
    calculateRowValues(item);

});
function calculateRowValues(el)
{
    var discount = el.find(".item-discount").val();
        var price = el.find(".item-price").val();
        var count = el.find(".item-count").val();
        var value = price * count;
        var netto = value - (value *(discount/100));
        var vat = el.find(".item-vat").val()/100;
        if (!vat)
        {
            vat = 0;
        }
        var tax = netto * vat;
        if (!tax)
        {
            tax = 0;
        }
        
        var brutto = netto * (1 + vat);
        
        el.find(".item-netto").val(netto);
        el.find(".item-brutto").val(brutto);
        
  
    el.find(".item-netto").val(netto);
    el.find(".item-brutto").val(brutto);
}
');

$this->registerCss('
.item-price { min-width: 110px; }
');