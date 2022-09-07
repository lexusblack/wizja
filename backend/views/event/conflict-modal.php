<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="row" style="margin-bottom:50px; text-align:center;">
<h2><?=Yii::t('app', 'Wybierz sposób rozwiązania konfliktu sprzętu ').$conflict->gear->name?></h2>
<h4><?=Yii::t('app', 'Liczba brakujących: ').$conflict->quantity?></h4>
<div class="col-md-6" style="text-align:center; margin-top:25px;">
<?=Html::a('Wybierz zamiennik', ['warehouse/assign', 'id'=>$conflict->event_id, 'type'=>'event', 'conflict'=>$conflict->id, 'c'=>$category], ['class'=>['btn btn-xl btn-success']])?>
</div>
<div class="col-md-6" style="text-align:center; margin-top:25px;">
<?=Html::a('Wypożycz', ['outer-warehouse/assign', 'id'=>$conflict->event_id, 'type'=>'event', 'conflict'=>$conflict->id, 'c'=>$category], ['class'=>['btn btn-xl btn-success']])?>
</div>
</div>

<?php if (($crn['cw'])||($crn['cw2'])){ ?>
<div class="row">
<div class="col-md-12" style="overflow-y:scroll; height:300px;">
<table class="table">
<tr style="background-color:#273a4a; color:white;"><td colspan=5><?=Yii::t('app', 'Podobny sprzęt w Cross Rental Network w pobliżu Twojego magazynu')?></td></tr>
<tr>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Liczba sztuk')?></th>
    <th><?=Yii::t('app', 'Firma')?></th>
    <th></th>
</tr>
<?php foreach ($crn['cw'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental', 'id'=>$cr->id, 'event_id'=>$conflict->event_id, 'gear_id'=>$conflict->gear_id, 'conflict_id'=>$conflict->id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
<?php foreach ($crn['cw2'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental', 'id'=>$cr->id, 'event_id'=>$conflict->event_id, 'gear_id'=>$conflict->gear_id, 'conflict_id'=>$conflict->id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
</table>
</div>
</div>
<?php    } ?>

<?php if (($crn['ce'])||($crn['ce2'])){ ?>
<div class="row">
<div class="col-md-12" style="overflow-y:scroll; height:300px;">
<table class="table">
<tr style="background-color:#273a4a; color:white;"><td colspan=5><?=Yii::t('app', 'Podobny sprzęt w Cross Rental Network w pobliżu miejsca eventu')?></td></tr>
<tr>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Liczba sztuk')?></th>
    <th><?=Yii::t('app', 'Firma')?></th>
    <th></th>
</tr>
<?php foreach ($crn['ce'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental', 'id'=>$cr->id, 'event_id'=>$conflict->event_id, 'gear_id'=>$conflict->gear_id, 'conflict_id'=>$conflict->id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
<?php foreach ($crn['ce2'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental', 'id'=>$cr->id, 'event_id'=>$conflict->event_id, 'gear_id'=>$conflict->gear_id, 'conflict_id'=>$conflict->id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
</table>
</div>
</div>
<?php    } ?>
<?php if (($crn['ceall'])){ ?>
<div class="row">
<div class="col-md-12" style="overflow-y:scroll; height:300px;">
<table class="table">
<tr style="background-color:#273a4a; color:white;"><td colspan=5><?=Yii::t('app', 'Podobny sprzęt w Cross Rental Network')?></td></tr>
<tr>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Liczba sztuk')?></th>
    <th><?=Yii::t('app', 'Firma')?></th>
    <th></th>
</tr>
<?php foreach ($crn['ceall'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental', 'id'=>$cr->id, 'event_id'=>$conflict->event_id, 'gear_id'=>$conflict->gear_id, 'conflict_id'=>$conflict->id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
</table>
</div>
</div>
<?php    } ?>
<?php $this->registerJs('
    $(".send-crn-request").click(function(e)
    {
        e.preventDefault();
        $.get($(this).attr("href"), function(data){
                openMessageDialog(data.id, 2);
            }); 
        $("#conflict_resolve_modal").modal("hide");
    })
    ');

$this->registerJs('
    $(".crn-book").click(function(e)
    {
        e.preventDefault();
        var modal = $("#outer_modal");
        var $link=$(this).attr("href");
        modal.modal("show");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("<?=$spinner?>");
        modal.find(".modalContent").load($link); 
        $("#conflict_resolve_modal").modal("hide");
    })
    ');