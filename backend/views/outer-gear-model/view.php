<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\OuterGearModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outer-gear-model-view">
    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' .  Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' .  Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
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
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content">
                                <p><strong><?=  Yii::t('app', 'Wymiary') ?> [<?=  Yii::t('app', 'cm') ?>]:</strong> <?php echo  Yii::t('app', "sz").":".$model->width.", ". Yii::t('app', 'wys').":".$model->height.", ". Yii::t('app', 'gł').":".$model->depth ?></p>
                                <p><strong><?=  Yii::t('app', 'Waga') ?> [<?=  Yii::t('app', 'kg') ?>]:</strong><?php echo $model->weight; ?></p>
                                <p><strong><?=  Yii::t('app', 'Pobór prądu') ?> [<?=  Yii::t('app', 'W') ?>]:</strong><?php echo $model->power_consumption; ?></p>
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
        if ($model->type==3){
        $tabItems = [
            [
                'label'=>'<i class="fa fa-cogs"></i> '. Yii::t('app', 'Dostawcy'),
                'content'=>$this->render('_tabItems', ['model'=>$model]),
                'active'=>true,
            ],
            [
                'label'=>'<i class="fa fa-info"></i> '. Yii::t('app', 'Informacje'),
                'content'=>$this->render('_tabInfo', ['model'=>$model]),
                'active'=>false,
            ]
        ];
        }else{
        $tabItems = [
            [
                'label'=>'<i class="fa fa-cogs"></i> '. Yii::t('app', 'Firmy'),
                'content'=>$this->render('_tabItems', ['model'=>$model]),
                'active'=>true,
            ],
            [
                'label'=>'<i class="fa fa-info"></i> '. Yii::t('app', 'Informacje'),
                'content'=>$this->render('_tabInfo', ['model'=>$model]),
                'active'=>false,
            ]
        ];
    }
        $tabItems[] = [
                'label'=> Yii::t('app', 'Tłumaczenia'),
                'content'=>$this->render('_tabTranslate', ['model'=>$model]),
                'active'=>false,
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