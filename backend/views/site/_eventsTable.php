<?php

use common\models\Meeting;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use common\models\Event;
use common\models\Rent;
use common\models\Personal;
$user = Yii::$app->user;
echo GridView::widget([
    'striped'=>false,
    'condensed'=>true,
    'bordered'=>false,
    'layout'=>'{items}',
    'dataProvider'=> $data,
    'columns'=>[
        [
            'label' => Yii::t('app', 'Nazwa'),
            'format' => 'html',
            'value' => function ($model) {
                if ($model['type'] == Event::getClassTypeLabel()) {
                    $content = Html::a($model['name'], ['event/view', 'id' => $model['id']]);
                    return $content;
                }
                if ($model['type'] == Rent::getClassTypeLabel()) {
                    $content = Html::a($model['name'], ['rent/view', 'id' => $model['id']]);
                    return $content;
                }
                if ($model['type'] == Personal::getClassTypeLabel()) {
                    return Html::a($model['name'], ['personal/view', 'id' => $model['id']]);
                }
                if ($model['type'] == Meeting::getClassTypeLabel()) {
                    return Html::a($model['name'], ['meeting/view', 'id' => $model['id']]);
                }
            }
        ],
        'type:text:'.Yii::t('app', 'Wydarzenie'),
        [
            'header' => Yii::t('app', 'Pakowanie <br> Odbiór'),
            'value' => function ($model) { return $model['pack']; },
            'format' => 'html',
            'contentOptions'=>['style'=>'min-width: 90px;']
        ],        
        [
            'header' => Yii::t('app', 'Trwa'),
            'value' => function ($model) { return $model['dateRange']; },
            'format' => 'html',
            'contentOptions'=>['style'=>'min-width: 130px;']
        ],
        [
            'header' => Yii::t('app', 'Demontaż <br> Zwrot'),
            'value' => function ($model) { return $model['disassembly']; },
            'format' => 'html',
            'contentOptions'=>['style'=>'min-width: 90px;']
        ],
        [
            'header' => "",
            'value' => function ($model) { 
                $content = "";
            if ($model['type'] == Event::getClassTypeLabel()) {
                        $content =Html::a("Wydaj", ['outcomes-warehouse/create', 'event'=>$model['id']], ['class'=>'btn btn-xs btn-success', 'style'=>'font-size:8px; margin-bottom:1px;'])."<br/>".Html::a("Przyjmij", ['incomes-warehouse/create', 'event'=>$model['id']], ['class'=>'btn btn-xs btn-success', 'style'=>'font-size:8px; margin-bottom:1px;']);
                }
                if ($model['type'] == Rent::getClassTypeLabel()) {
                        $content=Html::a("Wydaj", ['outcomes-warehouse/create', 'rent'=>$model['id']], ['class'=>'btn btn-xs btn-success', 'style'=>'font-size:8px; margin-bottom:1px;'])."<br/>".Html::a("Przyjmij", ['incomes-warehouse/create', 'rent'=>$model['id']], ['class'=>'btn btn-xs btn-success', 'style'=>'font-size:8px; margin-bottom:1px;']);
                    }
                    return $content;
             },
            'format' => 'html',
            'contentOptions'=>['style'=>'min-width: 50px;'],
            'visible'=>$user->can('gearWarehouseIncomesView')
        ]
    ]
]);