<?php

use yii\helpers\Html;
$header_fields = explode(";", $model->offerDraft->header_pdf_fields);
?>
<div class="header">
    <table class="table half_cell">
        <tr>
        <td>
        <?php if (in_array('logo', $header_fields)){ ?>
        <?php if (isset($model->firm_id)){ ?>
            <div class="logo"><?= isset($model->firm->logo) ? Html::img(\Yii::getAlias('@uploadroot' . '/settings/').$model->firm->logo,['height'=>'100']) : '';?></div>
        <?php }else{ ?>
            <div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploadroot' . '/settings/').$settings['companyLogo']->value,['height'=>'100']) : '';?></div>
        <?php } ?>
        <?php } ?>
        </td>
            <td>
                <table class="table half_cell">
                <?php if (in_array('name', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Nazwa') ?>:</td>
                        <td><?=str_replace("|", "", $model->name)?></td>
                    </tr>
                <?php } ?>
                
                <?php if (in_array('number', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Numer oferty') ?></td>
                        <td><?=$model->id?></td>
                    </tr>
                <?php } ?>
                <?php if (in_array('termin', $header_fields)){ ?>
            <?php if ($model->term_to) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Oferta ważna do') ?>:
                    </td>
                    <td>
                        <?=$model->term_to?>
                    </td>
                </tr>
            <?php } ?>
                <?php } ?>
                <?php if (in_array('paying_date', $header_fields)){ ?>
            <?php if ($model->payment_days) { ?>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Termin płatności') ?>:
                    </td>
                    <td>
                        <?=$model->payment_days." ".Yii::t("app", "dni")?>
                    </td>
                </tr>
            <?php } ?>
                <?php } ?>
                <?php if (in_array('datetime', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Data sporządzenia oferty') ?>:</td>
                        <td><?=$model->offer_date?></td>
                    </tr>
                <?php } ?>
                <?php if (in_array('page', $header_fields)){ ?>
                    <tr>
                        <td><?= Yii::t('app', 'Strona') ?>:</td>
                        <td>{PAGENO}</td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
    </table>
</div>