<?php

use yii\helpers\Html;

?>
    <div class="footer">
        <hr>
        <div class="b2_5 fl">
            <b><?= isset($settings['companyName']) ? $settings['companyName']->value : ''?></b><br>
            <?= isset($settings['companyAddress']) ? $settings['companyAddress']->value : ''?><br>
            <?= Yii::t('app', 'NIP') ?>: <?= isset($settings['companyNIP']) ? $settings['companyNIP']->value : ''?>
        </div>
        <div class="b2_5 fl">
            <b> <?= Yii::t('app', 'Dział handlowy') ?>:</b><br>
                <?= Yii::t('app', 'tel') ?>: <?= isset($settings['salesDepartmentPhone']) ? $settings['salesDepartmentPhone']->value : '' ?><br>
                <?= Yii::t('app', 'e-mail') ?>: <?= isset($settings['salesDepartmentEmail']) ? $settings['salesDepartmentEmail']->value : '' ?><br>
        </div>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Konto bankowe') ?>:</b><br>
            <?= isset($settings['companyBankName']) ? $settings['companyBankName']->value : '' ?><br>
            <?= isset($settings['companyBankNumber']) ? $settings['companyBankNumber']->value : '' ?>
        </div>
        <div class="b2_5 fl">
            <b><?= Yii::t('app', 'Kierownik projektu') ?>:</b><br>
            <?=$model->manager->first_name?> <?=$model->manager->last_name?><br>
            <?= Yii::t('app', 'e-mail') ?>:<?=$model->manager->email?><br>
            <?= Yii::t('app', 'tel') ?>:<?=$model->manager->phone?>
        </div>
    </div>
