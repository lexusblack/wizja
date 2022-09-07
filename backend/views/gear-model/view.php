<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\GearModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Baza sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-model-view">

    <div class="row">
    <div class="col-md-4">
        <div class="row">
        <div class="col-sm-12" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Import'), ['import', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?php if (Yii::$app->params['companyID']=='newsystem'){ ?>
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])
            ?>
            <?php } ?>
        </div>
        <div class="col-sm-12" style="margin-top: 15px">
            
            <h1><?=$this->title; ?></h1>
        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content">
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?php echo Yii::t('app', "sz").":".$model->width.", ".Yii::t('app', 'wys').":".$model->height.", ".Yii::t('app', 'gł').":".$model->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Objętość') ?> [<?= Yii::t('app', 'm')  ?>3]:</strong><?php echo $model->volume; ?></p>
                                <p><strong><?= Yii::t('app', 'Waga') ?> [<?= Yii::t('app', 'kg') ?>]:</strong><?php echo $model->weight; ?></p>
                                <p><strong><?= Yii::t('app', 'Pobór prądu') ?> [<?= Yii::t('app', 'W') ?>]:</strong><?php echo $model->power_consumption; ?></p>
                        </div>
                        <?php if ($model->getPhotoUrl()) { ?>
                            <div class="ibox-content no-padding border-left-right">
                                <img alt="image" class="img-responsive" src="<?php echo $model->getPhotoUrl(); ?>">
                            </div>
                        <?php } ?>
                    </div>
            </div>

        </div>
    </div>
    <div class="col-md-8">
    <div class="tabs-container">
        <?php
        $tabItems = [
            [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Informacje'),
                'content'=>$this->render('_tabInfo', ['model'=>$model]),
                'active'=>true,
            ],
            [
                'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Załączniki'),
                'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                'active'=>false,
            ],
        ];


        echo TabsX::widget([
            'items'=>$tabItems,
            'encodeLabels'=>false,
        ]);
        ?>
    </div>
    </div>
</div>
</div>
