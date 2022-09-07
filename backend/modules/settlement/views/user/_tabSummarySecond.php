<?php

use common\models\SettlementUser;
use common\models\User;
use kartik\grid\GridView;
use yii\helpers\Html;
$formatter = Yii::$app->formatter;
use yii\bootstrap\Modal;

/* @var $model \common\models\Event; */
Modal::begin([
    'id' => 'new-payment',
    'header' => Yii::t('app', 'Dodaj płatność'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */
/* @var $user \common\models\User */

?>
            <div class="row">
                <div class="col-12">
                <div class="ibox">
                <div class="ibox-content">
                <p><input id="myInput" type="text" placeholder="<?=Yii::t('app', 'Szukaj...')?>"></p>
                <table class="table" id="salary-table">
                <tr><th></th><th><?=Yii::t('app', 'Imię i Nazwisko')?></th><th><?=Yii::t('app', 'Suma godzin')?></th><th><?=Yii::t('app', 'Pensja')?></th><th><?=Yii::t('app', 'Prowizja')?></th><th><?=Yii::t('app', 'Prowizja niezaakc.')?></th><th><?=Yii::t('app', 'Dodatkowe funkcje')?></th><th><?=Yii::t('app', 'Diety')?></th><th><?=Yii::t('app', 'Koszty dodatkowe')?></th><th><?=Yii::t('app', 'Suma')?></th><th><?=Yii::t('app', 'Suma netto')?><th><?=Yii::t('app', 'Suma brutto')?></th><th><?=Yii::t('app', 'Zapłacono')?></th><th><?=Yii::t('app', 'Pozostało')?></th><th><?=Yii::t('app', 'FV')?></th></tr>
                <?php
foreach ($userSum as $model) {
        if ($model['status'])
        {
            if ($model['status']==1)
                $style="style='background-color:#d4edda'";
            else
                $style="style='background-color:#98eead'";
        }else{
            $style = "";
        }
         ?>
         <tr <?=$style?>>
         <td><input type="checkbox" class="salary-checkbox" data-hours="<?=$model['hours']?>" data-salary="<?=$model['salary']?>" data-roleaddons="<?=$model['roleAddons']?>" data-allowances="<?=$model['allowances']?>" data-addons="<?=$model['addons']?>" data-sum="<?=$model['sum']?>" data-sumbrutto="<?=$model['sum_brutto']?>" data-topay="<?=$model['user']->getPaymentsSum($year, $month)?>" data-paid="<?=$model['sum_vat']-$model['user']->getPaymentsSum($year, $month)?>" data-sumvat="<?=$model['sum_vat']?>"></td>
         <td><?=Html::a($model['user']->getDisplayLabel(),['/settlement/user/show', 'userId'=>$model['user']->id, 'year'=>$year, 'month'=>$month]) ?></td>
         <td><?=$model['hours']?>h</td>
         <td><?=$formatter->asCurrency($model['salary'])?></td>
         <td><?=$formatter->asCurrency($model['provision'])?></td>
         <td><?=$formatter->asCurrency($model['provision_non'])?></td>
         <td><?=$formatter->asCurrency($model['roleAddons'])?></td>
        <td><?=$formatter->asCurrency($model['allowances'])?></td>
        <td><?=$formatter->asCurrency($model['addons'])?></td>
        <td><?=$formatter->asCurrency($model['sum'])?></td>
        <td><?=$formatter->asCurrency($model['sum_brutto'])?></td>
        <td><?=$formatter->asCurrency($model['sum_vat'])?></td>
        <td><?php echo Html::a($formatter->asCurrency($model['user']->getPaymentsSum($year, $month)), ['/settlement/user/add-payment', 'user_id'=>$model['user']->id, 'year'=>$year, 'month'=>$month], ['class'=>'add-payment add-payment-'.$model['user']->id]) ?></td>
        <td><?php echo $formatter->asCurrency($model['sum_vat']-$model['user']->getPaymentsSum($year, $month)) ; ?></td>
        <td><?php $fvs = $model['user']->getExpensesFV($year, $month); 
                    foreach ($fvs as $fv)
                    {
                        echo Html::a($fv->number, ['/finances/expense/view', 'id'=>$fv->id])."<br/>";
                    }
                    echo Html::a(Yii::t('app', 'Dodaj fakturę'), ['/finances/expense/create', 'user_id'=>$model['user']->id, 'year'=>$year, 'month'=>$month]);

            ?></td>
        </tr>         

        <?php
}

?>
         <tr style="background-color: #f3f3f4; font-weight:bold;">
         <td></td>
         <td colspan="5"><?= Yii::t('app', 'Pracownicy ze stałą pensją') ?></td>

         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
        </tr> 
                <?php
foreach ($userNormal as $model) {

            if ($model->role!=30)
            {
                $total = $model->rate_amount;

                $brutto = ($total+5*$model->nfz_rate/36)/(1-$model->tax_rate/100)+$model->zus_rate;
                $brutto2 = $total+$model->nfz_rate+$model->zus_rate;
                            if ($brutto>$brutto2)
                            {
                                $podatek = $brutto-$brutto2;
                            }else{
                                $podatek = 0;
                                $brutto = $brutto2;
                            }
                $vat= $brutto*(1+$model->vat_rate/100);
             ?>
             <tr style='background-color:#fafafa'>
             <td><input type="checkbox" class="salary-checkbox" data-hours="0" data-salary="<?=round($total,0)?>" data-roleaddons="0" data-allowances="0" data-addons="0" data-sum="<?=round($total,0)?>" data-sumbrutto="<?=$brutto?>" data-sumvat="<?=$vat?>" data-topay="<?=$model->getPaymentsSum($year, $month)?>" data-paid="<?=$vat-$model->getPaymentsSum($year, $month)?>"></td>
             <td><?=$model->getDisplayLabel() ?></td>
             <td></td>
             <td><?=$formatter->asCurrency($total)?></td>
             <td></td>
             <td></td>
             <td></td>
            <td></td>
            <td></td>
            <td><?=$formatter->asCurrency($total)?></td>
            <td><?=$formatter->asCurrency($brutto)?></td>
            <td><?=$formatter->asCurrency($vat)?></td>
            <td><?php echo Html::a($formatter->asCurrency($model->getPaymentsSum($year, $month)), ['/settlement/user/add-payment', 'user_id'=>$model->id, 'year'=>$year, 'month'=>$month], ['class'=>'add-payment add-payment-'.$model->id]) ?></td>
            <td><?php echo $formatter->asCurrency($vat-$model->getPaymentsSum($year, $month)) ; ?></td>
            <td><?php $fvs = $model->getExpensesFV($year, $month); 
                    foreach ($fvs as $fv)
                    {
                        echo Html::a($fv->number, ['/finances/expense/view', 'id'=>$fv->id])."<br/>";
                    }
                    echo Html::a(Yii::t('app', 'Dodaj fakturę'), ['/finances/expense/create', 'user_id'=>$model->id, 'year'=>$year, 'month'=>$month]);

            ?></td>
            </tr>         

            <?php
            if (is_numeric($model->rate_amount))
            {
                $data['summary']['salary']+=floatval($total);
                $data['summary']['sum']+=floatval($total);
                $data['summary']['sum_brutto']+=floatval($brutto);
                $data['summary']['sum_vat']+=floatval($vat);            
            }
        }
            

        
}

?>
         <tr style="background-color: #f3f3f4; font-weight:bold;" id="salary-sum">
         <td></td>
         <td><?= Yii::t('app', 'Suma zaznaczonych') ?></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
        </tr> 
         <tr class="newsystem-bg">
         <td></td>
         <td><?= Yii::t('app', 'Podsumowanie') ?></td>
         <td><?= $data['summary']['hours']; ?>h</td>
         <td><?= $formatter->asCurrency($data['summary']['salary']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['provision']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['provision_non']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['roleAddons']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['allowances']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['addons']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['sum']); ?></td>
         <td><?=$formatter->asCurrency( $data['summary']['sum_brutto']); ?></td>
         <td><?= $formatter->asCurrency($data['summary']['sum_vat']); ?></td>
         <td><?= $formatter->asCurrency(\common\models\UserPayment::getMonthAmount($year, $month) )?> 
                <?= \common\models\UserPayment::getMonthAmountByTypes($year, $month) ?>
         </td>
         <td><?= $formatter->asCurrency($data['summary']['sum_vat']-\common\models\UserPayment::getMonthAmount($year, $month) )?> </td>
         <td></td>
        </tr> 
</table>
</div>
</div>
                </div>
            </div>
<?php
$this->registerJs('


$(".salary-checkbox").click(function(){
    var hours = 0;
    var salary = 0;
    var roleAddons = 0;
    var allowances = 0;
    var addons = 0;
    var sum = 0;
    var sum_brutto = 0;
    var sum_vat = 0;
    var sum_paid = 0;
    var sum_to_pay = 0;
    $("#salary-table").find(".salary-checkbox").each(function(){
        if ($(this).prop("checked"))
        {
            hours+=$(this).data("hours");
            salary+=$(this).data("salary");
            roleAddons+=$(this).data("roleaddons");
            allowances+=$(this).data("allowances");
            addons+=$(this).data("addons");
            sum+=$(this).data("sum");
            sum_brutto+=$(this).data("sumbrutto");
            sum_vat+=$(this).data("sumvat");
            sum_to_pay+=$(this).data("topay");
            sum_paid+=$(this).data("paid");
        }
    });
    $("#salary-sum").find("td:nth-child(3)").html(hours+"h");
    $("#salary-sum").find("td:nth-child(4)").html(parseFloat(salary).toFixed(2));
    $("#salary-sum").find("td:nth-child(5)").html(parseFloat(roleAddons).toFixed(2));
    $("#salary-sum").find("td:nth-child(6)").html(parseFloat(allowances).toFixed(2));
    $("#salary-sum").find("td:nth-child(7)").html(parseFloat(addons).toFixed(2));
    $("#salary-sum").find("td:nth-child(8)").html(parseFloat(sum).toFixed(2));
    $("#salary-sum").find("td:nth-child(9)").html(parseFloat(sum_brutto).toFixed(2));
    $("#salary-sum").find("td:nth-child(10)").html(parseFloat(sum_vat).toFixed(2));
    $("#salary-sum").find("td:nth-child(11)").html(parseFloat(sum_to_pay).toFixed(2));
    $("#salary-sum").find("td:nth-child(12)").html(parseFloat(sum_paid).toFixed(2));
});

');

$this->registerJs('
    $(".add-payment").click(function(e){
        e.preventDefault();
        $("#new-payment").modal("show").find(".modalContent").load($(this).attr("href"));
    });

$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#salary-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
');
?>