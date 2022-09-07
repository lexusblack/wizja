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
                        <td><?= Yii::t('app', 'Nazwa') ?>:</td>
                        <td><?=$model->name?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Numer') ?>:</td>
                        <td><?=$model->id?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Data oferty') ?>:</td>
                        <td><?=$model->offer_date?></td>
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