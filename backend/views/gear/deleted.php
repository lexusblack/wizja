<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Usunięte modele sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Model sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-index">

    <div class="panel_mid_blocks">
    <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
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
            [
                'attribute' => 'info',
                'label' => Yii::t('app', 'Dlaczego usunięto'),
                'format'=>'html'
            ],
            [
                'attribute' => 'update_time',
                'label' => Yii::t('app', 'Data usunięcia'),
                'format'=>'html'
            ],
        ],
    ]); ?>
</div>
    </div>
</div>
