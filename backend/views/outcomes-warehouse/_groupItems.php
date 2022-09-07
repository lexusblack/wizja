<?php
/* @var $this \yii\web\View */
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;


// przedmioty w case

if ($model->id != $activeGroup)
{
    return false;
}
//                if ($model->getGearItems()->count() == 0)
//                {
//                    return false;
//                }
echo GridView::widget([
    'dataProvider' => $gearGroupItemDataProvider,
    'options'=>[
        'class'=>'grid-view grid-view-group-items',
    ],
    'filterModel' => null,
    'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
    'columns' => [
        'id',
        'name',
        'number',
        'code',
        [
            'header' => Yii::t('app', 'Numer QR/Bar'),
            'value'=>function($model) {
                return $model->getBarCodeValue();
            },
        ],
        'serial',
//                        'status',
        'location',
//                        'test_date',
        'tester',
        'test_status',
//                        'service:ntext',
        'lamp_hours',
        'info:ntext',
        [
            'header'=>Yii::t('app', 'Najbliższe działania'),
        ],
        'purchase_price',
        'refund_amount',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delete} {remove} {service}',
            'urlCreator' =>  function($action, $model, $key, $index)
            {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                $params[0] = 'gear-item/' . $action;

                return Url::toRoute($params);
            },
            'buttons'=> [
                'remove'=> function ($url, $model, $key)
                {
                    return Html::a(Html::icon('remove'), ['gear-group/item-remove', 'id'=>$model->id]);
                },
                'history' => function ($url, $model, $key) {
                    return Html::a(Html::icon('list'), $url);
                },
                'service' => function ($url, $model, $key)
                {
                    return Html::a(\kartik\helpers\Html::icon('wrench'), $url);
                }
            ],
            'visibleButtons' => [
                'remove' => Yii::$app->user->can('gearCaseRemoveItem'),
                'history' => Yii::$app->user->can('gearItemHistory'),
                'service' => Yii::$app->user->can('gearItemServiceCreate'),
                'view' => Yii::$app->user->can('gearItemView'),
                'delete' => Yii::$app->user->can('gearItemDelete'),
                'update' => Yii::$app->user->can('gearItemEdit'),
            ]
        ],
    ],
]);