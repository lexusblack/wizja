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
                        <td>              
                        <?php      $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                        echo $start.' - '.$end; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>