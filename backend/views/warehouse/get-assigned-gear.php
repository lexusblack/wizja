<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>

<table class="table">
<tr><td colspan="4" class="newsystem-bg"><?=Yii::t('app','Zarezerwowany sprzęt')?></td></tr>
    <tr>
    <th>#</th>
    <th><?= Yii::t('app', 'Zdjęcie') ?></th>
    <th><?= Yii::t('app', 'Nazwa') ?></th>
    <th><?= Yii::t('app', 'Liczba') ?></th>
    </tr>
<tbody>
            <?php $i=0; foreach ($gears as $gear){ $i++;?>
            <tr data-itemouterid="<?= $gear->id ?>">
                <td><?=$i?>.</td>
                <td><?php
                if ($gear->gear->photo != null) {
                    echo Html::img($gear->gear->getPhotoUrl(), ['width' => '50px']);
                }  ?></td>
                <td><?= $gear->gear->name ?></td>
                <td><?= $gear->quantity ?></td>
            </tr>
            <?php } ?>
</tbody>
</table>
<?php if ($type=='event'){ ?>
<table class="table">
<tr><td colspan="4" class="red-bg"><?=Yii::t('app','Konflikty')?></td></tr>
    <tr>
    <th>#</th>
    <th><?= Yii::t('app', 'Zdjęcie') ?></th>
    <th><?= Yii::t('app', 'Nazwa') ?></th>
    <th><?= Yii::t('app', 'Liczba') ?></th>
    </tr>
<tbody>
            <?php $i=0; foreach ($conflicts as $gear){ $i++;?>
            <tr data-itemouterid="<?= $gear->id ?>">
                <td><?=$i?>.</td>
                <td><?php
                if ($gear->gear->photo != null) {
                    echo Html::img($gear->gear->getPhotoUrl(), ['width' => '50px']);
                }  ?></td>
                <td><?= $gear->gear->name ?></td>
                <td><?= $gear->quantity ?></td>
            </tr>
            <?php } ?>
</tbody>
</table>
<?php } ?>