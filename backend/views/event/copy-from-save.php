<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">

<div class="row">
    <div class="col-md-12">
        <div class="row">
        <p>
        <?= Html::a(Yii::t('app', 'Odśwież i zamknij okno'), "#", ['onclick'=>'location.reload(); return false;', 'class'=>'btn btn-warning'])?>
        </p>
        <?php if ($output){?>
        <h5><?=Yii::t('app', 'Sprzęt wewnętrzny')?></h5>
        <table class="table">
            <tr><th><?=Yii::t('app', 'Sprzęt')?></th><th><?=Yii::t('app', 'Zarezerwowano')?></th><th><?=Yii::t('app', 'W konflikcie')?></th></tr>
            <?php foreach ($output as $o) { 
            if (($o['result'])&&(!$o['conflict']))
                $style = "background-color:#d4edda";
            if (!$o['result'])
                $style = "background-color:#f8d7da";
            if (($o['result'])&&($o['conflict']))
                $style = "background-color:#fff3cd";
                ?>
            <tr style="<?=$style?>">
                <td><?=$o['gear']->gear->name?></td>
                <?php if (($o['result'])&&(!$o['conflict'])){ ?>
                <td><?=$o['gear']->quantity?></td>
                <td>0</td>
                <?php } ?>
                <?php if (!$o['result']){ ?>
                <td>0</td>
                <td>0</td>
                <?php } ?>
                <?php if (($o['result'])&&($o['conflict'])){ ?>
                <td><?=$o['conflict']->added?></td>
                <td><?=$o['conflict']->quantity?></td>
                 <?php    } ?>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        <?php if ($output_outer){?>
        <h5><?=Yii::t('app', 'Sprzęt zewnętrzny')?></h5>
        <table class="table">
            <tr><th><?=Yii::t('app', 'Sprzęt')?></th><th><?=Yii::t('app', 'Dodano')?></th></tr>
            <?php foreach ($output_outer as $o) { 
                $style = "background-color:#d4edda";
                ?>
            <tr style="<?=$style?>">
            <td><?=$o->outerGearModel->name?></td>
            <td><?=$o->quantity?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        <?php if ($output_extra){?>
        <h5><?=Yii::t('app', 'Sprzęt Dodatkowy')?></h5>
        <table class="table">
            <tr><th><?=Yii::t('app', 'Sprzęt')?></th><th><?=Yii::t('app', 'Dodano')?></th></tr>
            <?php foreach ($output_extra as $o) { 
                $style = "background-color:#d4edda";
                ?>
            <tr style="<?=$style?>">
            <td><?=$o->name?></td>
            <td><?=$o->quantity?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        <?php if ($output_users){?>
        <h5><?=Yii::t('app', 'Pracownicy przypisani do wydarzenia:')?></h5>
        <table class="table">
            <tr><th><?=Yii::t('app', 'Imię i nazwisko')?></th><th><?=Yii::t('app', 'Okres')?></th></tr>
            <?php foreach ($output_users as $o) { 
                $style = "background-color:#d4edda";
                ?>
            <tr style="<?=$style?>">
            <td><?=$o->user->displayLabel?></td>
            <td><?=$o->start_time."<br/>".$o->end_time?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        <?php if ($output_users_not){?>
        <h5><?=Yii::t('app', 'Pracownicy zajęci w tym czasie:')?></h5>
        <table class="table">
            <tr><th><?=Yii::t('app', 'Imię i nazwisko')?></th><th><?=Yii::t('app', 'Okres')?></th></tr>
            <?php foreach ($output_users_not as $o) { 
                $style = "background-color:#fff3cd";
                ?>
            <tr style="<?=$style?>">
            <td><?=$o->user->displayLabel?></td>
            <td><?=$o->start_time."<br/>".$o->end_time?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        </div>

</div>
</div>
</div>