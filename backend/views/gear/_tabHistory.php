<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Historia użycia'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <h2><?=Yii::t('app', 'Wydarzenia')?></h2>
            <table class="table table-striped">
            <tr>
                <th>#</th>
                <th><?=Yii::t('app', 'Wydarzenie')?></th>
                <th><?=Yii::t('app', 'Sztuk')?></th>
                <th><?=Yii::t('app', 'Klient')?></th>
                <th><?=Yii::t('app', 'Data')?></th>
            </tr>
            <?php 
            $i = 1;
            foreach ($model->eventGears as $e){ 
                $start = Yii::$app->formatter->asDateTime($e->start_time,'short');
                $end = Yii::$app->formatter->asDateTime($e->end_time, 'short');
                ?>
            <tr>
                <td><?=$i++?></td>
                <td><?=Html::a($e->event->name, ['/event/view', 'id'=>$e->event_id])?></td>
                <td><?=$e->quantity?></td>
                <?php if (isset($e->event->customer)) { ?>
                <td><?=Html::a($e->event->customer->name, ['/customer/view', 'id'=>$e->event->customer_id])?></td>
                <?php }else{ ?>
                <td></td><?php } ?>
                <td><?=$start.' <br /> '.$end?></td>
            </tr>
            <?php } ?>
            </table>
            </div>
        </div>
    </div>
</div>
</div>