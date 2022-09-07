<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model common\models\Vehicle */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pojazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="vehicle-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php
            if ($model->status)
                        echo Html::a('<i class="fa fa-wrench"></i> ' . Yii::t('app', 'Wyślij na serwis'), ['service', 'id' => $model->id], [
                                    'class' => 'btn btn-success',
                                ]);
                else {
                        echo Html::a('<i class="fa fa-wrench"></i> ' . Yii::t('app', 'Powrót z serwisu'), ['service-return', 'id' => $model->id], [
                                    'class' => 'btn btn-success',
                                ]);                } ?>
    </p>

<div class="row">
    <div class="col-md-4">
        <div class="ibox">
        <div class="ibox-title">
            <h5><?=  Yii::t('app', 'Szczegóły') ?></h5>
            <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
            </div>
    </div>
    <div class="ibox-content no-padding">
            <?= DetailView::widget([
                'model' => $model,
                'options' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
                ],
                'attributes' => [
                    'name',
                    'name_in_offer',
                    [
                        'attribute'=>'photo',
                        'value' =>  Html::img($model->getPhotoUrl(), ['style'=>'width: 300px;']),
                        'format'=>'html',
                    ],

                    [
                        'attribute'=>'registration_number',
                    ],
                    [
                        'attribute'=>'vin_number',
                    ],
                    [
                        'attribute'=>'capacity',
                    ],
                    [
                        'attribute'=>'volume',
                    ],
                    [
                        'attribute'=>'fuel_consumption',
                    ],
                    [
                        'attribute'=>'inspection_date',
                    ],
                    [
                        'attribute'=>'oc_date',
                    ],
                    'price_km:currency',
                    'price_city:currency',
                    [
                        'attribute'=>'reminderLabel',
                        'format'=>'text',
                        'label'=> Yii::t('app', 'Przypomnij'),
                    ],
                    [
                        'attribute'=>'price_rent',
                        'visible'=>Yii::$app->user->can('vehicleFinanceData'),
                    ],
                    [
                        'attribute'=>'typeLabel',
                        'format'=>'text',
                        'label'=> Yii::t('app', 'Typ'),
                    ],
                ],
            ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="tabs-container">
                <?php
                $tabItems = [];
                $tabItems[] = ['label' => '<i class="fa fa-wrench"></i> '.Yii::t('app', 'Wydarzenia'),
                        'content' => $this->render('_tabEvents', ['model' => $model]), 'options' => ['id' => 'tab-event',], 'active' => true];
                if ($user->can('fleetAttachments')) {
                    $tabItems[] = ['label' => '<i class="fa fa-paperclip"></i> '.Yii::t('app', 'Załączniki'),
                        'content' => $this->render('_tabAttachment', ['model' => $model]),  'options' => ['id' => 'tab-attachment',]];
                }
                    $tabItems[] = ['label' => '<i class="fa fa-wrench"></i> '.Yii::t('app', 'Naprawy'),
                        'content' => $this->render('_tabService', ['model' => $model]), 'options' => ['id' => 'tab-service',]];
                        $tabItems[] = [
                    'label'=> Yii::t('app', 'Tłumaczenia'),
                    'content'=>$this->render('_tabTranslate', ['model'=>$model]),
                    'active'=>false,
                ];
                echo TabsX::widget([
                    'items'=>$tabItems,
                    'encodeLabels'=>false,
                    'enableStickyTabs'=>true,
                ]);
                ?>
            </div>
        </div>
    </div>

</div>
