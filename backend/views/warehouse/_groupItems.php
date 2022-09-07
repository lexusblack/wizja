<?php
/* @var $this \yii\web\View */
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\editable\Editable;
use common\models\GearService;
use common\models\GearItem;

?>

<?php
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
                        'number:text:'.Yii::t('app', 'Nr'), 
                        [
                            'header' => Yii::t('app', 'Nazwa'),
                            'format' => 'html',
                            'value' => function ($gear) {
                                $service = GearService::getCurrentModel($gear->id);
                                if ($gear->status == GearItem::STATUS_NEED_SERVICE) {
                                    return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]) . " " . Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }
                                if ($gear->status == GearItem::STATUS_SERVICE) {
                                    return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]) . " " . Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }

                                return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]). " " . Html::a(Yii::t('app', 'Wyślj na serwis'), ['/gear-service/create', 'id'=>$gear->id], ['class'=>'label label-primary']);
                            }
                        ],                       
                        [
                            'header' => Yii::t('app', 'Sprawdzony'),
                            'format' => 'html',
                            'class'=>\kartik\grid\EditableColumn::className(),
                           'value' => function ($gear) {
                                $date = "";
                                if ($gear->test_date)
                                    $date = " (".date("d.m.Y", strtotime($gear->test_date)).")";
                                return $gear->tester.$date;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => Yii::t('app', 'imię i nazwisko sprawdzającego'),
                                    'name'=>'tester',
                                    'formOptions' => [
                                            'action'=>['/gear-item/test', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Godziny lamp'),
                            'format' => 'html',
                            'class'=>\kartik\grid\EditableColumn::className(),
                            'value' => function ($gear) {
                                return $gear->lamp_hours;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'name'=>'lamp_hours',
                                    'header' => Yii::t('app', 'godziny lamp'),
                                    'formOptions' => [
                                            'action'=>['/gear-item/lamps', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Uwagi'),
                            'class'=>\kartik\grid\EditableColumn::className(),
                            'format' => 'html',
                            'value' => function ($gear) {
                                return $gear->info;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'name'=>'info',
                                    'inputType' => Editable::INPUT_TEXTAREA,
                                    'formOptions' => [
                                            'action'=>['/gear-item/info', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delete} {remove}',
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
            ],
            'visibleButtons' => [
                'remove' => Yii::$app->user->can('gearCaseRemoveItem'),
                'view' => Yii::$app->user->can('gearItemView'),
                'delete' => Yii::$app->user->can('gearItemDelete'),
                'update' => Yii::$app->user->can('gearItemEdit'),
            ]

        ],
    ],
]);