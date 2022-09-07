<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$itemsOut = [];
?>
<h3><?=Yii::t('app', 'Zapotrzebowanie:')?> <?=$total?> <?=Yii::t('app', 'szt,')?></h3>
<?php if ((!$groups)&&(!$items)){ ?>
<div class="alert alert-danger">
                                <?=Yii::t('app', 'Brak dostępnych ezgemplarzy w magazynie')?>
                            </div>
<?php } ?>
<?php if ($groups) { ?>
<table class="table">
<tr><td colspan="5" class="newsystem-bg"><?=Yii::t('app','Dostępne case')?></td></tr>
    <tr>
    <th></th>
    <th></th>
    <th><?= Yii::t('app', 'Nazwa') ?></th>
    <th><?= Yii::t('app', 'Numery') ?></th>
    <th><?= Yii::t('app', 'Kod') ?></th>
    </tr>
<tbody>
            <?php $i=0; foreach ($groups as $group){ $i++;?>
            <tr data-id="<?= $group->id ?>">
                <td><input type="checkbox" class="gear-modal-group" data-id="<?=$group->id?>"/></td>
                <td><?php echo Html::icon('arrow-down', ['class' => 'row-warehouse-out-modal', 'style' => 'cursor: pointer;', 'id'=>$group->getBarCodeValue()]); ?>
                </td>
                <td><?=$group->name?></td>
                <td><?=$group->itemNumbers?></td>
                <td><?=$group->getBarCodeValue()?></td>
            </tr>
            <tr style="display: none;" class="sub_models">
                        <td colspan="5">
                            <table class="table">
                                <tr>
                                <th></th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th><?= Yii::t('app', 'Numer') ?></th>
                                <th><?= Yii::t('app', 'Kod') ?></th>
                                </tr>
                            <tbody>
                                        <?php $i=0; foreach ($group->gearItems as $item){ ?>
                                        <tr data-id="<?= $item->id ?>">
                                            <td><input type="checkbox" class="gear-modal-item" data-id="<?=$item->id?>"/></td>
                                            <td><?=$item->name?></td>
                                            <td><?=$item->number?></td>
                                            <td><?=$item->getBarCodeValue()?></td>
                                        </tr>
                                        <?php }?>
                            </tbody>
                            </table>
                        </td>
            </tr>
            <?php } ?>
</tbody>
</table>
<?php } ?>
<?php if ($items) { ?>
<table class="table">
<tr><td colspan="4" class="newsystem-bg"><?=Yii::t('app','Dostępne egzemplarze')?></td></tr>
    <tr>
    <th></th>
    <th><?= Yii::t('app', 'Nazwa') ?></th>
    <th><?= Yii::t('app', 'Numer') ?></th>
    <th><?= Yii::t('app', 'Kod') ?></th>
    </tr>
<tbody>
            <?php $i=0; foreach ($items as $item){ ?>
            <tr data-id="<?= $item->id ?>">
                <td><input type="checkbox" class="gear-modal-item" data-id="<?=$item->id?>"/></td>
                <td><?=$item->name?></td>
                <td><?=$item->number?></td>
                <td><?=$item->getBarCodeValue()?></td>
            </tr>
            <?php 
                } ?>
</tbody>
</table>
<?php } ?>

<?php if (($itemsService)) { ?>
<table class="table">
<tr><td colspan="4" class="red-bg"><?=Yii::t('app','Niedostępne egzemplarze')?></td></tr>
    <tr>
    <th><?= Yii::t('app', 'Nazwa') ?></th>
    <th><?= Yii::t('app', 'Numer') ?></th>
    <th><?= Yii::t('app', 'Kod') ?></th>
    <th><?= Yii::t('app', 'Powód') ?></th>
    </tr>
<tbody>
            <?php foreach ($itemsService as $item){ ?>
            <tr data-id="<?= $item->id ?>">
                <td><?=$item->name?></td>
                <td><?=$item->number?></td>
                <td><?=$item->getBarCodeValue()?></td>
                <td><?=Yii::t('app', 'W serwisie')?></td>
            </tr>
            <?php  }?>
</tbody>
</table>
<?php } ?>
<?= Html::a(Yii::t('app', 'Dodaj'), '#', ['class' => 'btn btn-primary', 'id'=>'button-gear-outcome-modal']) ?>


