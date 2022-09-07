<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Schematy zadań');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-schema-index">
    <p>
        <?= Html::a(Yii::t('app', 'Dodaj nowy'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<div class="row">
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title newsystem-bg">
            <h3><?=Yii::t('app', 'Szablony zadań dla projektów')?></h3>
            </div>
            <div class="ibox-content">
            <table class="table">
            <?php $i=0; foreach($projectSchemas as $ps){ $i++; ?>
            <tr><td><?=$i?>.</td><td><?=Html::a($ps->name, ['project', 'id'=>$ps->id])?><?php if ($ps->default) echo Yii::t('app', " (domyślny)");?></td><td><?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $ps->id])?> <?=Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$ps->id], [
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])?></td></tr>
             <?php   }?>
            </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title newsystem-bg">
            <h3><?=Yii::t('app', 'Szablony zadań dla wydarzeń')?></h3>
            </div>
            <div class="ibox-content">
            <table class="table">
            <?php $i=0; foreach($eventSchemas as $ps){ $i++; ?>
            <tr><td><?=$i?>.</td><td><?=Html::a($ps->name, ['project', 'id'=>$ps->id])?><?php if ($ps->default) echo Yii::t('app', " (domyślny)");?></td><td><?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $ps->id])?> <?=Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$ps->id], [
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])?></td></tr>
             <?php   }?>
            </table>
            </div>
        </div>
    </div>
    </div>
<div class="row">
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title newsystem-bg">
            <h3><?=Yii::t('app', 'Szablony zadań dla wypożyczeń')?></h3>
            </div>
            <div class="ibox-content">
            <table class="table">
            <?php $i=0; foreach($rentalSchemas as $ps){ $i++; ?>
            <tr><td><?=$i?>.</td><td><?=Html::a($ps->name, ['project', 'id'=>$ps->id])?><?php if ($ps->default) echo Yii::t('app', " (domyślny)");?></td><td><?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $ps->id])?> <?=Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$ps->id], [
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])?></td></tr>
             <?php   }?>
            </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title newsystem-bg">
            <h3><?=Yii::t('app', 'Szablony zadań dla spotkań')?></h3>
            </div>
            <div class="ibox-content">
            <table class="table">
            <?php $i=0; foreach($meetingSchemas as $ps){ $i++; ?>
            <tr><td><?=$i?>.</td><td><?=Html::a($ps->name, ['project', 'id'=>$ps->id])?><?php if ($ps->default) echo Yii::t('app', " (domyślny)");?></td><td><?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $ps->id])?> <?=Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$ps->id], [
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])?></td></tr>
             <?php   }?>
            </table>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title newsystem-bg">
            <h3><?=Yii::t('app', 'Szablony zadań dla serwisów')?></h3>
            </div>
            <div class="ibox-content">
            <table class="table">
            <?php $i=0; foreach($serviceSchemas as $ps){ $i++; ?>
            <tr><td><?=$i?>.</td><td><?=Html::a($ps->name, ['project', 'id'=>$ps->id])?><?php if ($ps->default) echo Yii::t('app', " (domyślny)");?></td><td><?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $ps->id])?> <?=Html::a('<i class="fa fa-trash"></i>', ['delete', 'id'=>$ps->id], [
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])?></td></tr>
             <?php   }?>
            </table>
            </div>
        </div>
    </div>
    </div>
</div>

