<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-form">


    <div class="row">
        <div class="col-md-12">


          

        </div>
        
    </div>


</div>
<div>
<?php if (count($notAdded)>0) { ?>
<h3>Nieprzeniesiony sprzęt (nie ma tylu egzemplarzy)</h3>
<table class="table">
<?php foreach ($notAdded as $gear){
?>
<tr><td><?=$gear['gear']->name?></td><td><?=$gear['quantity']." ".Yii::t('app', 'szt.')?></td><td><?php if (!$gear['gear']->no_items){ echo "["; foreach ($gear['items'] as $item){ echo $item->number.",";} echo "]";} ?></td></tr>
<?php
    } ?>
</table>
</div>
<?php } ?>
<div>
<h3>Przeniesiony sprzęt</h3>
<table class="table">
<?php foreach ($added as $gear){
?>
<tr><td><?=$gear['gear']->name?></td><td><?=$gear['quantity']." ".Yii::t('app', 'szt.')?></td><td><?php if (!$gear['gear']->no_items){ echo "["; foreach ($gear['items'] as $item){ echo $item->number.",";} echo "]";} ?></td></tr>
<?php
    } ?>
</table>
</div>
