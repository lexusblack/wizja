<?php

use yii\helpers\Html;
$footer_fields = explode(";", $model->offerDraft->footer_pdf_fields);

?>
    <div class="footer">
        <hr>

        <?php if (isset($model->firm_id)){ ?>
        <?php if (in_array('address', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= $model->firm->name ?></b><br>
            <?= $model->firm->address?><br>
            <?= $model->firm->zip." ".$model->firm->city?><br>
            <?= Yii::t('app', 'NIP') ?>: <?= $model->firm->nip ?>
        </div>
        <?php } ?>
        <?php if (in_array('email', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b> <?= Yii::t('app', 'Dział handlowy') ?>:</b><br>
                <?= Yii::t('app', 'tel') ?>: <?= $model->firm->phone ?><br>
                <?= Yii::t('app', 'e-mail') ?>: <?= $model->firm->email ?><br>
        </div>
        <?php } ?>
        <?php if (in_array('bank', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Konto bankowe') ?>:</b><br>
            <?= $model->firm->bank_name ?><br>
            <?= $model->firm->bank_number ?>
        </div>
        <?php } ?>
        <?php }else{ ?>
        <?php if (in_array('address', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= isset($settings['companyName']) ? $settings['companyName']->value : ''?></b><br>
            <?= isset($settings['companyAddress']) ? $settings['companyAddress']->value : ''?><br>
            <?= isset($settings['companyZip']) ? $settings['companyZip']->value : ''?> <?= isset($settings['companyCity']) ? $settings['companyCity']->value : ''?><br>
            <?= Yii::t('app', 'NIP') ?>: <?= isset($settings['companyNIP']) ? $settings['companyNIP']->value : ''?>
        </div>
        <?php } ?>
        <?php if (in_array('email', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b> <?= Yii::t('app', 'Dział handlowy') ?>:</b><br>
                <?= Yii::t('app', 'tel') ?>: <?= isset($settings['salesDepartmentPhone']) ? $settings['salesDepartmentPhone']->value : '' ?><br>
                <?= Yii::t('app', 'e-mail') ?>: <?= isset($settings['salesDepartmentEmail']) ? $settings['salesDepartmentEmail']->value : '' ?><br>
        </div>
        <?php } ?>
        <?php if (in_array('bank', $footer_fields)){ ?>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Konto bankowe') ?>:</b><br>
            <?= isset($settings['companyBankName']) ? $settings['companyBankName']->value : '' ?><br>
            <?= isset($settings['companyBankNumber']) ? $settings['companyBankNumber']->value : '' ?>
        </div>
        <?php } ?>
        <?php } ?>
        <?php if (in_array('manager', $footer_fields)){
        if ($model->manager_id) {?>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Kierownik projektu') ?>:</b><br>
            <?=$model->manager->first_name?> <?=$model->manager->last_name?><br>
            <?php if (strlen($model->manager->email)<30) { ?>
            <?=$model->manager->email?><br>
            <?php }else{ ?>
            <span style="font-size:10px"><?=$model->manager->email?></span><br>
            <?php } ?>
            <?= Yii::t('app', 'tel') ?>:<?=$model->manager->phone?>
        </div>
        <?php } }?>
        <div><?=$settings['footerText']->value?></div>

    </div>
