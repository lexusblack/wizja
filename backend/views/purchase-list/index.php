<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\PurchaseListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
use kartik\editable\Editable;

$this->title = Yii::t('app', 'Listy zakupowe');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="search-form" style="display:none">
        <?=  $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'label'=>Yii::t('app', 'Nr'),
        'value'=>function($model){
            return "LZ/".substr($model->datetime, 0,4)."/".$model->id;
        }],
        [
                'attribute'=>'name',
                'format'=>'raw',
                'value'=>function($model)
                {
                    return Html::a($model->name, ['view', 'id'=>$model->id]);
                }
        ],
        'datetime',
        [
            'label'=>Yii::t('app', 'Dodał'),
            'value'=>function ($model)
            {
                if (isset($model->user))
                {
                    return $model->user->displayLabel();
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
                                    'action'=>['/purchase-list/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\PurchaseList::getStatusList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                //'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\PurchaseList::getStatusList(),
                    'filterWidgetOptions' => [
                        // 'data'=>\common\models\PurchaseList::getStatusList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                'value'=>function($model)
                {
                    return \common\models\PurchaseList::getStatusList()[$model->status];
                }
        ],
        [
                'label'=>Yii::t('app', 'Liczba pozycji'),
                'value'=> function($model){
                    return count($model->purchaseListItems);
                }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-purchase-list']],
        'export' => false,
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]) ,
        ],
    ]); ?>

</div>
