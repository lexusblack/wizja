<?php
use kartik\nav\NavX;
use yii\bootstrap\NavBar;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
/* @var $this \yii\web\View; */
\common\assets\JqueryQueryObjectAsset::register($this);
$user = Yii::$app->user;
?>

<div class="row">
<div class="col-lg-4">
    <?php if ($user->can('menuInvoicesInvoiceCreate')) { ?>
        <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-newsystem" id="invoice-type-dropdown"><?= Yii::t('app', 'Wystaw') ?>
            <b class="caret"></b></a>
        <?= Dropdown::widget(['items' => \backend\modules\finances\Module::getInvoiceSubitems(), 'options' => ['id' => 'invoice-dropdown-menu']]);
    }
    ?>
<?php
if ($user->can('menuInvoicesInvoiceSend')) {
    echo Html::a(Html::icon('arrow-right') . " " . Yii::t('app', 'Wyślij'), ['invoice/send'], ['class' => 'btn btn-default']);
}
?>
</div>

<div class="col-lg-8">
    <?php echo \backend\modules\finances\widgets\SearchWidget::widget([
        'model' => $model,
    ]); ?>
</div>
</div>
<?php


$this->registerJs('
    $(".send-invoices").on("click", function(e){
        e.preventDefault();
        var keys = $("#invoices-grid").yiiGridView("getSelectedRows");
        if (keys.length) 
        {
            var url = $(this).prop("href");
    
            var q = $.query.load(url)
            for(i=0; i<keys.length; i++)
            {
               q = q.set("id[]", keys[i]);
            }
            
            var u =  url + q.toString();
            window.location = u;
        }
        else
        {
            alert("'.Yii::t('app', 'Nic nie zostało wybrane!').'");
        }
        
    });
    
    $("#invoice-dropdown-menu li a").on("click", function(e){
        e.preventDefault();
        var select = $(this).closest("li").data("select");
        if (!select)
        {
            if (select==0)
            {
                window.location = this.href;
            }
            return;
        }
        var keys = $("#invoices-grid").yiiGridView("getSelectedRows");
        
        if (keys.length) 
        {
            if ($("tr[data-key=\'"+keys[0]+"\']").data("correction") == 1) {
                alert("'.Yii::t('app', 'Nie można korygować faktury korygującej').'");
                return;
            }
            var url = $(this).prop("href");
    
            var q = $.query.load(url).set("invoiceId", keys[0]);
            var x = $.query.load(url).empty().toString();
            
            var u = url.split("?")[0] + q.toString(); 
            window.location = u;
        }
        else
        {
            alert("'.Yii::t('app', 'Nic nie zostało wybrane!').'");
        }
        
    });
    
    
');
?>
