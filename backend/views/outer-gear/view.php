<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = $model->outerGearModel->name." - ".$model->company->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = $this->title;

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="gear-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->outerGearModel->name; ?></h5>
                            <span class="label label-warning-light pull-right"><?php echo $model->company->name; ?></span>
                        </div>
                        <div class="ibox-content">
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?php echo "sz:".$model->outerGearModel->width.", wys:".$model->outerGearModel->height.", gł:".$model->outerGearModel->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Waga') ?> [<?= Yii::t('app', 'kg') ?>]:</strong><?php echo $model->outerGearModel->weight; ?></p>
                                <p><strong><?= Yii::t('app', 'Pobór prądu') ?> [<?= Yii::t('app', 'W') ?>]:</strong><?php echo $model->outerGearModel->power_consumption; ?></p>
                                <p><strong><?= Yii::t('app', 'Cena wynajmu') ?> [<?= Yii::t('app', 'PLN') ?>]:</strong><?php echo Yii::$app->formatter->asCurrency($model->price); ?></p>
                                <p><strong><?= Yii::t('app', 'Cena dla klienta') ?> [<?= Yii::t('app', 'PLN') ?>]:</strong><?php echo Yii::$app->formatter->asCurrency($model->selling_price);?></p>
                        <?php if ($model->outerGearModel->getPhotoUrl()) { ?>
                            <div class="ibox-content no-padding border-left-right">
                                <img alt="image" class="img-responsive" src="<?php echo $model->outerGearModel->getPhotoUrl(); ?>">
                            </div>
                        <?php } ?>
                    </div>
            </div>

        </div>
        </div>
        </div>
        <div class="col-md-8">
        <div class="tabs-container">
        <?php
        $tabItems = [
            [
                'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Firmy'),
                'content'=>$this->render('_tabEvents', ['model'=>$model]),
                'active'=>true,
            ]
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

