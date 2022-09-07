<?php

use yii\helpers\Html;

?>
<div class="header">
    <table class="table half_cell">
        <tr>
            <td><div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploadroot' . '/settings/').$settings['companyLogo']->value,['height'=>'100']) : '';?></div></td>
            <td>
                <table class="table half_cell">
                    <tr>
                        <td><?= Yii::t('app', 'Numer zamówienia') ?>:</td>
                        <td><?=$model->id?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Data zamówienia') ?>:</td>
                        <td><?=$model->create_at?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Strona') ?>:</td>
                        <td>{PAGENO}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>