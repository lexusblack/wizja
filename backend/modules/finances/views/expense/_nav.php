<?php
use kartik\nav\NavX;
use yii\bootstrap\NavBar;
use kartik\form\ActiveForm;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;

/* @var $this \yii\web\View; */
\common\assets\JqueryQueryObjectAsset::register($this);
$user = Yii::$app->user;
?>

<div class="row">
<div class="col-sm-4">
    <?php if ($user->can('menuInvoicesExpenseCreate')) { ?>
        <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-newsystem" id="invoice-type-dropdown"><?= Yii::t('app', 'Wystaw') ?>
            <b class="caret"></b></a>
        <?= Dropdown::widget(['items' => \backend\modules\finances\Module::getExpenseSubitems(),]);
    }
    ?>
<?php
if ($user->can('menuInvoicesExpenseSend')) {
    echo Html::a(Html::icon('arrow-right') . " " . Yii::t('app', 'Wyślij'), ['invoice/send'], ['class' => 'btn btn-default']);
}

?>
</div>

<div class="col-sm-8">
<div class="row">
    <?php echo \backend\modules\finances\widgets\SearchWidget::widget([
        'model' => $model,
    ]); ?>
</div>
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
            console.log(u, url);
            window.location = u;
        }
        else
        {
            alert("'.Yii::t('app', 'Nic nie zostało wybrane!').'");
        }
        
        
    });
    
    
');