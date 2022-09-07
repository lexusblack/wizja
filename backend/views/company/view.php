<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Instancje systemu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-view">

    <div class="row">
        <div class="col-sm-9">
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content">
                                <p><strong><?= Yii::t('app', 'Link') ?>:</strong><?php echo $model->link; ?> </p>
                                <p><strong><?= Yii::t('app', 'Identyfikator') ?>:</strong><?php echo $model->code; ?></p>
                                <p><strong><?= Yii::t('app', 'Mail') ?>:</strong><?php echo $model->mail; ?></p>
                                <p><strong><?=Yii::t('app', 'Telefon') ?>:</strong><?php echo $model->phone; ?></p>
                                <p><strong><?=Yii::t('app', 'Data rozpoczęcia') ?>:</strong><?php echo $model->start_date; ?></p>
                        </div>
                    </div>
            </div>

        </div>
        </div>
    <div class="col-md-8">
    <div class="tabs-container">
        <?php
        $tabItems =[
                [
                'label'=>'<i class="fa fa-bell"></i> '.Yii::t('app', 'Zgłoszone błędy'),
                'content'=>$this->render('_tabErrors', ['model'=>$model]),
                'active'=>true,
                ],
                [
                'label'=>'<i class="fa fa-map-marker"></i> '.Yii::t('app', 'Dodane miejsca'),
                'content'=>$this->render('_tabLocation', ['model'=>$model]),
                'active'=>false,
                ],
                [
                'label'=>'<i class="fa fa-globe"></i> '.Yii::t('app', 'Sprzęt w Cross Rental'),
                'content'=>$this->render('_tabCrossRental', ['model'=>$model]),
                'active'=>false,
                ],
                [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Statystyki'),
                'content'=>$this->render('_tabInfo', ['model'=>$model]),
                'active'=>false,
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
