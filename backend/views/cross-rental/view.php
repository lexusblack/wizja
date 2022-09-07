<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\GearModel */

$this->title = $model->gearModel->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cross Rental Network'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cr = $model;
$model = $cr->gearModel;
?>
<div class="gear-model-view">

    <div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-content">
                        <h1><?=$this->title; ?>
                        <?php
                            if ($cr->owner!=Yii::$app->params['companyID'])
                    echo Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request pull-right']]);
                ?>
                        </h1>
                                
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?php echo Yii::t('app', "sz").":".$model->width.", ".Yii::t('app', 'wys').":".$model->height.", ".Yii::t('app', 'gł').":".$model->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Objętość') ?> [<?= Yii::t('app', 'm')  ?>3]:</strong><?php echo $model->volume; ?></p>
                                <p><strong><?= Yii::t('app', 'Waga') ?> [<?= Yii::t('app', 'kg') ?>]:</strong><?php echo $model->weight; ?></p>
                                <p><strong><?= Yii::t('app', 'Pobór prądu') ?> [<?= Yii::t('app', 'W') ?>]:</strong><?php echo $model->power_consumption; ?></p>
                        </div>
                        <?php if ($model->getPhotoUrl()) { ?>
                            <div class="ibox-content no-padding border-left-right">
                            <a href="<?php echo $model->getPhotoUrl(); ?>" data-gallery=""><img class="img-responsive" src="<?php echo $model->getPhotoUrl(); ?>" alt=""></a>
                            </div>
                        <?php } ?>
                    </div>
            </div>

        </div>
        <div class="widget lazur-bg p-xl">

                                <h2>
                                    <?=$cr->owner_name?>
                                </h2>
                        <ul class="list-unstyled m-t-md">
                            <li>
                                <span class="fa fa-envelope m-r-xs"></span>
                                <label>Email:</label>
                                <?=$cr->owner_mail?>
                            </li>
                            <li>
                                <span class="fa fa-home m-r-xs"></span>
                                <label>Adres:</label>
                                <?=$cr->owner_address?>
                            </li>
                            <li>
                                <span class="fa fa-phone m-r-xs"></span>
                                <label>Tel:</label>
                                <?=$cr->owner_phone?>
                            </li>
                        </ul>

                    </div>
    </div>
    <div class="col-md-8">
    <div class="tabs-container">
        <?php
        $tabItems = [
            [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Informacje'),
                'content'=>$this->render('/gear-model/_tabInfo', ['model'=>$model]),
                'active'=>true,
            ],
            [
                'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Załączniki'),
                'content'=>$this->render('/gear-model/_tabAttachment', ['model'=>$model]),
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

                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>

<?php $this->registerJs('
    $(".send-crn-request").click(function(e)
    {
        e.preventDefault();
        $.get($(this).attr("href"), function(data){
                openMessageDialog(data.id, 2);
            }); 
    })
    ');