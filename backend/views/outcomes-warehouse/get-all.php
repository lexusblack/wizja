<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\ArrayHelper;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OutcomesForCustomer;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\IncomesGearOur;
use common\models\IncomesGearOuter;
use common\models\IncomesForCustomer;
use common\models\IncomesForEvent;
use common\models\IncomesForRent;
use backend\models\OutcomesGearGeneral;
use common\models\Event;
use common\models\Rent;


/* @var $this yii\web\View */
/* @var $model common\models\OutcomesWarehouse */
/* @var $modelsGear backend\models\OutcomesGearGeneral */
/* @var $event int */
/* @var $rent int */
/* @var $gear int */
/* @var $outer_gear int */
/* @var $group_gear int */



$this->title = Yii::t('app', 'Sprzęt niezwrócony do magazynu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn'), 'url' => ['/warehouse/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-warehouse-get-all">
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title newsystem-bg">
                            <h4><?= Html::encode($this->title) ?></h4>
                        </div>
                        <div class="ibox-content">
                        <?php if (count($gears)>0) { ?>
                        <table class="table table-striped">
                        <thead>
                            <tr><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Wydano na')?></th></tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <?php foreach($gears as $gear){ ?>
                        <tr>
                        <td><?=Html::a($gear->name, ['/gear/view', 'id'=>$gear->id])?></td>
                        <td>
                        <?php
                                $outcomed1 = \common\models\EventGearOutcomed::find()->where(['>', 'quantity', 0])->andWhere(['gear_id'=>$gear->id])->all();
                                $outcomed2 = \common\models\RentGearOutcomed::find()->where(['>', 'quantity', 0])->andWhere(['gear_id'=>$gear->id])->all();
                                foreach ($outcomed1 as $g)
                                {
                                     $numbers = \common\models\GearItem::find()->where(['event_id'=>$g->event_id, 'gear_id'=>$gear->id, 'packlist_id'=>$g->packlist_id])->orderBy(['number'=>SORT_ASC])->all();
                                    $num = "";
                                    foreach ($numbers as $n)
                                    {
                                        if ($num!="")
                                            $num.=", ";
                                        else
                                            $num = Yii::t("app", "Numery").": ";
                                        $num .=$n->number;
                                    }
                                    echo Html::a($g->event->name." [".$g->packlist->name."]", ['/event/view', 'id'=>$g->event_id])." ".$g->quantity." ".Yii::t('app', 'szt.')." ".$num."<br/>";
                                   
                                }
                                foreach ($outcomed2 as $g)
                                {
                                    $numbers = \common\models\GearItem::find()->where(['rent_id'=>$g->rent_id, 'gear_id'=>$gear->id])->orderBy(['number'=>SORT_ASC])->all();
                                    $num = "";
                                    foreach ($numbers as $n)
                                    {
                                        if ($num!="")
                                            $num.=", ";
                                        else
                                            $num = Yii::t("app", "Numery").": ";
                                        $num .=$n->number;
                                    }
                                    echo Html::a($g->rent->name, ['/rent/view', 'id'=>$g->rent_id])." ".$g->quantity." ".Yii::t('app', 'szt.')." ".$num."<br/>";
                                }
                        ?>
                        </td>
                        </tr>
                        <?php } ?>    
                        </table>
                        <?php }else{ ?>
                            <div class="alert alert-success">
                                <?=Yii::t('app', 'Cały sprzęt w magazynie! Gratulacje!')?>
                            </div>
                        <?php } ?>
                        </div>
                        </div>
            </div>
    </div>



</div>
