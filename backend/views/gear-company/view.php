<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearCompany */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Producent'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-company-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Yii::t('app', 'Producent sprzętu').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
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
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
    
    <div class="row">
<?php
if($providerGearModel->totalCount){
    $gridColumnGearModel = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
                [
                'attribute' => 'company_id',
                'label' => Yii::t('app', 'Producent'),
                'value' => function($model){
                    if ($model->company)
                    {return $model->company->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCompany::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Producent'), 'id' => 'grid-gear-model-search-comapany_id']
            ],
        'name',
        ['class'=>\common\components\grid\PhotoColumn::className()],
        'brightness',
        'power_consumption',
        [
                'attribute' => 'category_id',
                'label' => Yii::t('app', 'Kategoria'),
                'value' => function($model){
                    if ($model->category)
                    {return $model->category->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCategory::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Kategoria'), 'id' => 'grid-gear-model-search-category_id']
            ],
        'width',
        'height',
        'volume',
        'weight',
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ];
    echo Gridview::widget([
        'dataProvider' => $providerGearModel,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-model']],
        'export' => false,
        'columns' => $gridColumnGearModel
    ]);
}
?>
    </div>
</div>
