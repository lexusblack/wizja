<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model common\models\Offer */

$this->title = Yii::t('app', 'Wyślij Oferte');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
    $gearColumns = [
        [
            'attribute' => 'photo',
            'value' => function ($model, $key, $index, $column) {
                if ($model->photo == null)
                {
                    return '-';
                }
                return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
            },
            'format'=>'raw',
            'contentOptions'=>['class'=>'text-center'],
        ],
        [
            'attribute' => 'name',
            'value' => function ($model, $key, $index, $column) {
                $content = Html::a($model->name, ['/gear/update', 'id'=>$model->id]);
                return $content;
            },
            'format' => 'html',
        ],
        [
            'attribute'=>'quantity',
            'value'=>function($gear, $key, $index, $column)
            {
                /* @var $gear \common\models\Gear */
                if ($gear->no_items==true)
                {
                    return $gear->quantity;
                }
                else
                {
                    return $gear->getGearItems()->count();
                }
            }
        ],
        'brightness:decimal',
        'power_consumption:decimal',
        [
            'attribute'=>'weight',
            'value' => function ($model, $key, $index, $column)
            {
                if ($model->weight)
                {
                    return Yii::$app->formatter->asDecimal($model->weight).' '.Yii::t('app', 'kg');
                }
                return null;
            }
        ],
        [
            'attribute'=>'width',
            'value' => function ($model, $key, $index, $column)
            {
                if ($model->width)
                {
                    return Yii::$app->formatter->asDecimal($model->width).' '.Yii::t('app', 'cm');
                }
                return null;
            }
        ],
        [
            'attribute'=>'height',
            'value' => function ($model, $key, $index, $column)
            {
                if ($model->height)
                {
                    return Yii::$app->formatter->asDecimal($model->height).' '.Yii::t('app', 'cm');
                }
                return null;
            }
        ],
        [
            'attribute'=>'depth',
            'value' => function ($model, $key, $index, $column)
            {
                if ($model->depth)
                {
                    return Yii::$app->formatter->asDecimal($model->depth).' '.Yii::t('app', 'cm');
                }
                return null;
            }
        ],
        [
            'attribute'=>'volume',
            'value' => function ($model, $key, $index, $column)
            {
                if ($model->depth)
                {
                    return Yii::$app->formatter->asDecimal($model->getCalculatedVolume()).' '.Yii::t('app', 'cm').'<sup>3</sup>';
                }
                return null;
            },
            'format'=>'html',
        ],
        'price:currency',
    ];



    echo GridView::widget([
                'dataProvider'=>$gearDataProvider,
                'columns' => $gearColumns,
                'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $gearDataProvider, $event, $assignedItems, $offer, $warehouse)
            {
                // $activeModel = $warehouse->activeModel;
                $content = '';
                $rowOptions =  [
                    'class'=>'gear-details',
                ];
                if ($model->no_items == 0)
                {
                    $content = GridView::widget([
                        'layout'=>'{items}',
                        'dataProvider'=>$model->getGearItemDataProvider(),
                        'options'=>[
                            'class'=>'grid-view grid-view-items',
                        ],
                        'rowOptions'=> function ($model, $key, $index, $grid) use ($offer)
                        {
                            return [
                                'class'=> $model->isAvailable($offer) ? '' : 'danger',
                            ];
                        },
                        'filterModel' => null,
                        'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $gearDataProvider, $assignedItems, $event, $offer, $warehouse)
                        {

                            $ranges = $model->getUnavailableRanges($offer->term_from, $offer->term_to);
                            if ($ranges==false)
                            {
                                return false;
                            }
                            $content = implode(' ', $ranges);
                            $btnText = Html::icon('plus');
                            if ($model->isAssignedTo($offer) == true)
                            {
                                $m = $model->getAssignConnection($offer);
                                $formatter = Yii::$app->formatter;
                                $btnText = $formatter->asDatetime($m->start_time, 'short').'-'.$formatter->asDatetime($m->end_time, 'short');

                            }
                            $content .= ' '.Html::a($btnText, Url::to(['warehouse/custom-dates','itemId'=>$model->id, 'type'=>'offer', 'id'=>$event->id]), ['class'=>'btn btn-success btn-xs custom-dates', 'data'=>['item'=>$model->id]]);

                            $content .= ' '.$this->render('../../../../views/warehouse/_modal', ['warehouse'=>$warehouse, 'owner'=>$event, 'item'=>$model, 'btnText'=>$btnText]);
                            return Html::tag('tr', Html::tag('td', $content, ['colspan'=>16]));
                        },
                        'columns' => [
                            [
                                'headerOptions' => [
//                                    'class'=>'checkbox-item select-on-check-all',
                                ],
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) use ($assignedItems, $event) {
                                    /* @var $model \common\models\GearItem */
                                    return [
                                        'checked' => key_exists($model->id, $assignedItems),
                                        'class'=>'checkbox-item',
                                        'disabled'=>$model->isAvailable($event) ? false : true,
                                    ];
                                }
                            ],
                            'id',
                            'name',
                            'number:text:'.Yii::t('app', 'Nr'),
                            'code:text:'.Yii::t('app', 'Kod'),
                            'serial:text:'.Yii::t('app', 'Nr seryjny'),
                            [
                                'attribute' => 'location',
                                'label' => Yii::t('app', 'Miejsce w').'<br/>'.Yii::t('app', 'magazynie'),
                                'encodeLabel'=>false,
                            ],
                            'test_date',
                            'tester',
                            'test_status',
                            'service:ntext',
                            [
                                'attribute' => 'lamp_hours',
                                'label' => Yii::t('app', 'Akutalne').'<br/>'.Yii::t('app', 'godziny lamp'),
                                'encodeLabel'=>false,
                            ],
                            'info:ntext',
                            [
                                'header'=>Yii::t('app', 'Najbliższe').'<br/>'.Yii::t('app', 'działania'),
                            ],
                            [
                                'attribute' => 'purchase_price',
                                'label' => Yii::t('app', 'Cena').'<br/>'.Yii::t('app', 'zakupu'),
                                'encodeLabel'=>false,
                            ],
                            [
                                'attribute' => 'refund_amount',
                                'label' => Yii::t('app', 'Kwota').'<br/>'.Yii::t('app', 'zwrotu'),
                                'encodeLabel'=>false,
                            ],
                        ]
                    ]);
                }

                return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]), $rowOptions);
            },
        ]);?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
