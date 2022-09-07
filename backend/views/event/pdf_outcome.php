<?php
use yii\bootstrap\Html;

?>
<div class="row" style="margin-top:30px">
    <div class="col-md-12">
<h3><?=Yii::t('app', 'Status wydań')?>
</h3>


<table class="table table-row-border">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th style="text-align:center"><?=Yii::t('app', 'Na wydaniu')?></th>
    <th style="text-align:center"><?=Yii::t('app', 'Numery')?></th>
    <th style="text-align:center"><?=Yii::t('app', 'Wydane')?></th>
    <th style="text-align:center"><?=Yii::t('app', 'Zwrócone')?></th>
    
</tr>
<?php 
$i = 0;
$outcomes = \common\helpers\ArrayHelper::map(\common\models\OutcomesForEvent::find()->where(['event_id'=>$model->id])->asArray()->all(), 'outcome_id', 'outcome_id');
$incomes = \common\helpers\ArrayHelper::map(\common\models\IncomesForEvent::find()->where(['event_id'=>$model->id])->asArray()->all(), 'income_id', 'income_id');
foreach ($gears as $gear){ $i++; ?>
<tr>
   <td><?=$i?></td>
   <td><?=$gear->gear->name?></td>
<?php
$event_outcomes = \common\models\EventGearOutcomed::find()->where(['event_id'=>$model->id, 'gear_id'=>$gear->gear_id])->all();
$t = 0;
foreach ($event_outcomes as $eo)
{
    $t +=$eo->quantity;
}
$ids = \common\helpers\ArrayHelper::map(\common\models\GearItem::find()->where(['gear_id'=>$gear->gear->id])->asArray()->all(), 'id', 'id');
$outcomesGear = \common\models\OutcomesGearOur::find()->where(['outcome_id'=>$outcomes, 'gear_id'=>$ids])->all();
$out = 0;
foreach ($outcomesGear as $o)
{
    $out += $o->gear_quantity;
}
$incomesGear = \common\models\IncomesGearOur::find()->where(['income_id'=>$incomes, 'gear_id'=>$ids])->all();
$in = 0;
foreach ($incomesGear as $o)
{
    $in += $o->quantity;
}
$items = \common\models\GearItem::find()->where(['gear_id'=>$gear->gear->id, 'event_id'=>$model->id])->all();
$itemsString = "";
foreach ($items as $item)
{
    $itemsString .= "[".$item->number."] ";
}
?>
<td style="text-align:center"><?=$t?></td>
<td style="text-align:center"><?=$itemsString?></td>
<td style="text-align:center"><?=$out?></td>
<td style="text-align:center"><?=$in?></td>
</tr>

<?php    } ?>
        </table>
</div>
</div>