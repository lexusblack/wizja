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
                <div class="upf"><b><?= Yii::t('app', 'wypożyczający') ?>:</b></div>
                <h3>
                    <?=$model->company->name ?>
                </h3>
                <p><?=$model->company->address ?></p>
                <p><?=$model->company->zip ?> <?=$model->company->city ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$model->company->nip ?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->company->address ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->company->email ?></p>
            </div>
            <div class="hb fl">
                <div class="upf"><b><?= Yii::t('app', 'zamawiający') ?>:</b></div>
                <h3>
                    <?=$settings['companyName']->value ?>
                </h3>
                <p><?=$settings['companyAddress']->value ?></p>
                <p><?=$settings['companyCity']->value ?></p>
                <p><?= Yii::t('app', 'NIP') ?>: <?=$settings['companyNIP']->value ?></p>
                <p><?=$model->user->first_name?> <?=$model->user->last_name?></p>
                <p><?= Yii::t('app', 'tel') ?>: <?=$model->user->phone ?></p>
                <p><?= Yii::t('app', 'e-mail') ?>: <?=$model->user->email ?></p>
            </div>
        </div> 
             <div>
                <h3><u><?= Yii::t('app', 'Pozycje zamówienia') ?>:</u></h3>
                <table class="table table-row-border">
                    <tr>
                        <th><?= Yii::t('app', 'Lp.') ?></th>
                        <th><?= Yii::t('app', 'Nazwa') ?></th>
                        <th><?= Yii::t('app', 'Ilość') ?></th>
                        <th><center><?= Yii::t('app', 'Czas pracy') ?></center></th>
                        <th><center><?= Yii::t('app', 'Data odbioru') ?></center></th>
                        <th><center><?= Yii::t('app', 'Data zwrotu') ?></center></th>
                        <th><center><?= Yii::t('app', 'Cena') ?></center></th>
                    </tr>
                    <?php 
                    $i=0;
                    $total=0;
                    foreach ($model->eventOuterGears as $gear) {
                        $i++;
                        $total+=$gear->price;
                    ?>
                        <tr>
                            <td><?=$i?>.</td>
                            <td><?=$gear->outerGear->name?></td>
                            <td><center><?=$gear->quantity?></center></td>
                            <td style="padding:10px"><?= Yii::t('app', 'od') ?> <?=$gear->getDateFormatted($gear->start_time)?><br/><?= Yii::t('app', 'do') ?> <?=$gear->getDateFormatted($gear->end_time)?></td>
                            <td style="padding:10px"><?=$gear->getDateFormatted($gear->reception_time)?></td>
                            <td style="padding:10px"><?=$gear->getDateFormatted($gear->return_time)?></td>
                            <td style="padding:10px"><?=$gear->price?> <?= Yii::t('app', 'PLN') ?></td>
                        </tr>
                    <?php } ?>
                        <tr><td></td><td></td><td></td><td></td><td></td><td style="padding:10px; text-align:right;"><strong><?= Yii::t('app', 'SUMA') ?>:</strong></td><td><strong><?=$total?> <?= Yii::t('app', 'PLN') ?></strong></td></tr>
                </table>
            </div>       
        

    </div>


