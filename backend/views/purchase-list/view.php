<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;
use kartik\editable\Editable;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseList */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Listy zakupowe'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-list-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?=Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            <?= Html::a(Yii::t('app', 'PDF'), ['pdf', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
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
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'datetime',
        [
                'attribute'=>'status',
                'value'=>function($model)
                {
                    return \common\models\PurchaseList::getStatusList()[$model->status];
                }
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
    <p><?= Html::a(Yii::t('app', 'Dodaj pozycję'), ['/purchase-list-item/create', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
<?php
if($providerPurchaseListItem->totalCount){
    $gridColumnPurchaseListItem = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'label'=>Yii::t('app', 'S'),
                'attribute'=>'position',

                'content'=>function($model, $key, $index, $grid)
                {
                    return Html::a(Html::icon('chevron-up'), '#', ['class'=>'sort-up']).Html::a(Html::icon('chevron-down'), '#', ['class'=>'sort-down']);


                },
                'contentOptions'=>function ($model, $key, $index, $column) {
                        return [
                            'class'=>'text-center gear-sort',
                            'data-id'=>$model->id,
                            'style'=>'white-space:nowrap;'
                        ];
                },
            ],
            'name',
                            [
                'attribute' => 'quantity',
                'class'=>\kartik\grid\EditableColumn::className(),
                'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_TEXT,
                            'name'=>'quantity',
                            'formOptions' => [
                                    'action'=>['/purchase-list-item/desc', 'id'=>$model->id],
                                ],
                        ];
                    },
        ],
            'company_name',
            'company_address',
            [
                'attribute' => 'event.name',
                'format'=>'raw',
                'label' => 'Event',
                'value' => function ($model)
                {
                    if (isset($model->event))
                    {
                        return Html::a($model->event->name.' ['.$model->event->code.']', ['/event/view', 'id' => $model->event->id]);
                    }else{
                        return "-";
                    }
                }
            ],
            [
                'attribute'=>'status',
                'class'=>\kartik\grid\EditableColumn::className(),
                'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/purchase-list-item/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\PurchaseListItem::getStatusList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\PurchaseListItem::getStatusList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                'value'=>function($model)
                {
                    return \common\models\PurchaseListItem::getStatusList()[$model->status];
                }
        ],
        [
                'attribute' => 'description',
                'class'=>\kartik\grid\EditableColumn::className(),
                'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_TEXT,
                            'name'=>'description',
                            'formOptions' => [
                                    'action'=>['/purchase-list-item/desc', 'id'=>$model->id],
                                ],
                        ];
                    },
        ],
                [
                'attribute' => 'price',
                'class'=>\kartik\grid\EditableColumn::className(),
                'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_TEXT,
                            'name'=>'price',
                            'formOptions' => [
                                    'action'=>['/purchase-list-item/desc', 'id'=>$model->id],
                                ],
                        ];
                    },
        ],
            [
                'label'=>Yii::t('app', 'Łącznie'),
                'value'=>function($model){
                    return $model->quantity*$model->price;
                }
            ],
            [
            'class'=>\common\components\ActionColumn::className(),
            'controllerId'=>'purchase-list-item',
            'buttons' => [
                            'update'=>function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>', ['/purchase-list-item/update', 'id'=>$model->id]);
                            },
                            'delete'=>function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>', ['/purchase-list-item/delete', 'id'=>$model->id], ['data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],]);
                            },
                        ],
            'visibleButtons' => [
                        'update' => true,
                        'delete' => true,
                        'view'=>false
                ]
            ],
    ];
    echo Gridview::widget([
        'dataProvider' => $providerPurchaseListItem,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-purchase-list-item']],

        'export' => false,
        'columns' => $gridColumnPurchaseListItem
    ]);
}
?>

    </div>
</div>
<?php
$this->registerJs('
$(".gear-sort a").on("click", function(e) {
    e.preventDefault();
    var el = $(this);
    var row = el.closest("tr");

    if (el.hasClass("sort-up"))
    {
        var el2 = row.prev("tr");
        if (el2)
        {
            row.insertBefore( el2 );
        }
    }
    else if (el.hasClass("sort-down"))
    {
        var el2 = row.next("tr");
        if (el2)
        {
            row.insertAfter( el2 );
        }
        
    }
    
    var list = $(".gear-sort").map(function(){return $(this).data("id");}).get();
    $.post("'.Url::to(['purchase-list/order']).'", {data:list, _csrf: yii.getCsrfToken()});
    
    return false;
});
');
?>