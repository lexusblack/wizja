
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
                <th><?=Yii::t('app', 'Klient')?></th>
                <th><?=Yii::t('app', 'Data')?></th>
            </tr>
            <?php 
            $i = 1;
            foreach ($model->outcomesGearOurs as $e){ 
                            $rent = $e->outcome->getOutcomesForRents();
                            $event = $e->outcome->getOutcomesForEvents();
                            $customer = "";
                            $result = null;
                            if ($rent->count() == 1) {
                                $res = $rent->one();
                                $result = Html::a($res->rent->name.' ['.$res->rent->code.']', ['/rent/view', 'id' => $res->rent->id]);
                                $start = Yii::$app->formatter->asDateTime($res->rent->start_time,'short');
                                $end = Yii::$app->formatter->asDateTime($res->rent->end_time, 'short');
                                if (isset($res->rent->customer))
                                $customer =  Html::a($res->rent->customer->name, ['/customer/view', 'id'=>$res->rent->customer_id]); 
                            }
                            if ($event->count() == 1) {
                                $res = $event->one();
                                $result = Html::a($res->event->name.' ['.$res->event->code.']', ['/event/view', 'id' => $res->event->id]);
                                $start = Yii::$app->formatter->asDateTime($res->event->event_start,'short');
                                $end = Yii::$app->formatter->asDateTime($res->event->event_end, 'short');
                                if (isset($res->event->customer))
                                $customer = Html::a($res->event->customer->name, ['/customer/view', 'id'=>$res->event->customer_id]); 
                            }


                ?>
            <tr>
                <td><?=$i++?></td>
                <td><?=$result?></td>
                <td><?=$customer?></td>
                <td><?=$start.' <br /> '.$end?></td>
            </tr>
            <?php } ?>
            </table>
            </div>
        </div>
    </div>
</div>
</div>