<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Historia przesunięć'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <table class="table table-striped">
            <tr>
            <th><?=Yii::t('app', 'Data')?></th>
                <th><?=Yii::t('app', 'Typ')?></th>
                <th><?=Yii::t('app', 'Sztuk')?></th>
                <th><?=Yii::t('app', 'Magazyn z')?></th>
                <th><?=Yii::t('app', 'Magazyn do')?></th>
                <th><?=Yii::t('app', 'Wykonał')?></th>
                <?php if ($model->no_items==false) {?>
                <th><?=Yii::t('app', 'Numery')?></th><?php } ?>
                <th><?=Yii::t('app', 'Informacje')?></th>
            </tr>
            <?php 
            $i = 1;
            foreach ($model->gearMovements as $e){ 
                $start = Yii::$app->formatter->asDateTime($e->datetime,'short');
               // $end = Yii::$app->formatter->asDateTime($e->end_time, 'short');
                ?>
            <tr>
                <td><?=$start?></td>
                <td><?=$e->getTypeLabel()?></td>
                <td><?=$e->quantity?></td>
                <td><?php if ($e->warehouse_from){ echo $e->warehouseFrom->name;}?></td>
                <td><?php if ($e->warehouse_to){ echo $e->warehouseTo->name;}?></td>
                <td><?php echo $e->user->displayLabel; ?></td>
                <?php if ($model->no_items==false) {?>
                <td>
                    <?php foreach (\common\models\GearMovementItem::find()->where(['gear_movement_id'=>$e->id])->all() as $n)
                    {
echo $n->gearItem->number.",";
                        } ?>
                </td><?php } ?>
                <td><?=$e->info?></td>
            </tr>
            <?php } ?>
            </table>
            </div>
        </div>
    </div>
</div>
</div>