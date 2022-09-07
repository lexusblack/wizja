<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
$user = Yii::$app->user;
?>
<h3><?php echo $model->name ?></h3>
<div class="row">
    <div class="col-md-12 no-padding">
        <div class="panel_mid_blocks">
            <table class="table">
                <tr><th><?=Yii::t('app', 'Wydarzenie')?></th><th><?=Yii::t('app', 'Liczba')?></th><th><?=Yii::t('app', 'Numery')?></th></tr>
                <?php foreach ($events as $event)
                { ?>
                <tr>
                    <td><?=Html::a($event->event->name, ['/event/view', 'id'=>$event->event_id])?></td>
                    <td><?=$event->quantity?> <?=Yii::t("app", "szt.")?></td>
                    <?php
                    $numbers = \common\models\GearItem::find()->where(['event_id'=>$event->event_id, 'gear_id'=>$event->gear_id])->all();
                                    $num = "";
                                    foreach ($numbers as $n)
                                    {
                                        if ($num!="")
                                            $num.=", ";
                                        $num .=$n->number;
                                    }
                                    ?>
                     <td><?=$num?></td>
                </tr>

                    <?php }?>
                    <?php foreach ($rents as $rent)
                { ?>
                <tr>
                    <td><?=Html::a($rent->rent->name, ['/rent/view', 'id'=>$rent->rent_id])?></td>
                    <td><?=$rent->quantity?> <?=Yii::t("app", "szt.")?></td>
                    <?php
                    $numbers = \common\models\GearItem::find()->where(['rent_id'=>$rent->rent_id, 'gear_id'=>$event->gear_id])->all();
                                    $num = "";
                                    foreach ($numbers as $n)
                                    {
                                        if ($num!="")
                                            $num.=", ";
                                        $num .=$n->number;
                                    }
                                    ?>
                     <td><?=$num?></td>
                </tr>

                  <?php  }?>
            </table>
        </div>
    </div>
</div>