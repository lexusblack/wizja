<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

$formatter = Yii::$app->formatter;
use yii\helpers\Html;

?>
    <div class="pdf_box">
        <div class="client_info">
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'zamawiający') ?>:</b></div>
                <h3>
                    <?=$model->customer->name ?>
                </h3>
                <p><?=$model->customer->zip ?> <?=$model->customer->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->customer->nip ?></p>
                <p><?= Yii::t('app', 'mobile') ?>: <?=$model->customer->address ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->customer->email ?></p>
            </div>
            <div class="hb fl">
                <table class="table half_cell">
                    <tr>
                        <td><?= Yii::t('app', 'Kierownik projektu') ?>:</td>
                        <td><?=$model->manager->first_name?> <?=$model->manager->last_name?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'tel') ?>:</td>
                        <td><?=$model->manager->phone?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'e-mail') ?>:</td>
                        <td><?=$model->manager->email?></td>
                    </tr>
                </table>
            </div>
        </div> 
        
        <div class="main_info">
            <div class="name_box">
                <h1>
                    <?= Yii::t('app', 'Nazwa projektu') ?>: <?=$model->name?>
                </h1>
                <?php if ($model->location !== null): ?>
                <p><u><?= Yii::t('app', 'Mejsce i adres') ?></u></p>
                <p><?=$model->location->name?></p>
                <p><?=$model->location->city?>, <?=$model->location->zip?></p>
                <p><?=$model->location->address?></p>
                <?php endif; ?>
            </div>

        </div>

        <table class="table table-row-border offertable" cellpadding="5" cellspacing="0">
                <?php
                $total_summ_of_services = 0;
                $total_summ_of_services_provision = 0;
                $total_profit_of_services=0; ?>
                <?php foreach($offerForm->serviceCategories as $category): 
                    $summ_of_one_cat = 0;
                ?>
                <tr  style="background-color:<?=$category['color']?>;"><td colspan="5" style="color:white"><?=$category['name']?></td></tr>
                <tr style="font-weight:bold"><td>Nazwa</td><td>Ilość</td><td>Cena</td><td>Razem</td><td>Uwagi</td></tr>
                <?php foreach ($category['items'] as $service): ?>
                <tr><td><?=$service['name']?></td><td><?=$service['count']?></td><td><?=$service['client_price']?></td><td><?=$formatter->asCurrency($service['total_price'])?></td><td><?=$service['info']?></td></tr>
                <?php $summ_of_one_cat+=$service['total_price']; 
                            ?>
                <?php endforeach;?>
                <tr><td colspan="3">Razem <?=$category['name']?></td><td colspan="2"><?=$formatter->asCurrency($summ_of_one_cat)?></td>
                <?php 
                    $total_summ_of_services +=$summ_of_one_cat; 
                    if ($category['provizion'])
                        $total_summ_of_services_provision +=$summ_of_one_cat; 
                ?>
                <?php endforeach;?>
                    <?php
                    $sum_netto = $total_summ_of_services;
                    $provision = $total_summ_of_services_provision*$model->provision/100;
                    $vat = ($sum_netto+$provision)*0.23;
                    $sum_brutto = $sum_netto+$provision+$vat;
                    ?>
                <tr  style="background-color:#273a4a"><td colspan="5" style="color:white">Podsumowanie</td></tr>
                <tr><td>Suma netto: </td><td><?=$formatter->asCurrency($sum_netto)?></td></tr>
                <tr><td>Wynagrodzenie agencji [<?=$model->provision?>%]: </td><td><?=$formatter->asCurrency($provision)?></td></tr>
                <tr><td>Podatek VAT: </td><td><?=$formatter->asCurrency($vat)?></td></tr>
                <tr><td>Suma brutto: </td><td><?=$formatter->asCurrency($sum_brutto)?></td></tr>
        </table>

    </div>


