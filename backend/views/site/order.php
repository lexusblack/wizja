<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title =Yii::t('app', 'Zamówienie nr').' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zamówienia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <div class="row">
        <div class="col-sm-9">
        <?php if (!$model->confirm){ ?>
        <?= Html::a('<i class="fa fa-check"></i> ' . Yii::t('app', 'Potwierdź wszystko'), ['confirm', 'hash' => $model->hash], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        
            <h2><?=  Html::encode($this->title) ?></h2>

        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute' => 'company.name',
            'label' => Yii::t('app', 'Firma'),
        ],
        [
            'attribute' => 'contact.displaylabel',
            'label' => Yii::t('app', 'Osoba kontaktowa'),
        ],
        'confirm',
        'create_at',
        [
            'attribute' => 'user.username',
            'label' => Yii::t('app', 'Utworzył'),
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
    
    <div class="row">
<?php
if($providerEventOuterGear->totalCount){
    $gridColumnEventOuterGear = [
        ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'event.name',
                'label' => Yii::t('app', 'Wydarzenie')
            ],
            [
                'attribute' => 'outerGear.name',
                'label' => Yii::t('app', 'Sprzęt')
            ],
            [
                'attribute' => 'quantity',
                'label' => Yii::t('app', 'Ilość')
            ],
            [
                'attribute' => 'start_time',
                'label' => Yii::t('app', 'Czas pracy od')
            ],
            [
                'attribute' => 'end_time',
                'label' => Yii::t('app', 'Czas pracy do')
            ],
            [
                'attribute' => 'reception_time',
                'label' => Yii::t('app', 'Termin odbioru')
            ],
            [
                'attribute' => 'return_time',
                'label' => Yii::t('app', 'Termin zwrotu')
            ],
            [
                'attribute' => 'price',
                'label' => Yii::t('app', 'Cena')
            ],
            [
                'attribute' => 'confirm',
                'label' => Yii::t('app', 'Potwierdzenie'),
                'format'=>'html',
                'value' => function($model){
                    if ($model->confirm)
                    {return Yii::t('app', "TAK");}
                    else
                    {return Html::a('<i class="fa fa-check"></i> ' . Yii::t('app', 'Potwierdź'), ['confirmone', 'event_id' => $model->event_id, 'outer_gear_id'=>$model->outer_gear_id, 'hash'=>$model->order->hash], ['class' => 'btn btn-success btn-xs']);}
                },
            ],
    ];
    echo Gridview::widget([
        'dataProvider' => $providerEventOuterGear,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-event-outer-gear']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Lista sprzętu')),
        ],
        'export' => false,
        'columns' => $gridColumnEventOuterGear
    ]);
}
?>
    </div>
</div>
