<?php

use yii\helpers\Html;

?>
<div class="header">
    <table class="table half_cell">
        <tr>
            <td><div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploads' . '/settings/').$settings['companyLogo']->value,['height'=>'100']) : '';?></div></td>
            <td>
                <table class="table half_cell">
                    <tr>
                        <td><?= Yii::t('app', 'Nazwa eventu') ?>:</td>
                        <td><?=$model->name?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Data') ?>:</td>
                        <td><?=$model->start_time?></td>
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