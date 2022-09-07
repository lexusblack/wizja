<?php

use common\models\GearService;
use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Usunięte Egzemplarze Sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Egzemplarze'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-item-index">
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app', 'Nazwa'),
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->gear->no_items)
                        return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id]);
                    else
                        return Html::a($model->name, ['view', 'id'=>$model->id]);
                }
            ],
            [
                'attribute' => 'gear.name',
                'label' => Yii::t('app', 'Nazwa modelu'),
            ],
            [
                'label' => Yii::t('app', 'Kategoria'),
                'attribute' => 'gear.category.name',
            ],
            'number',
            [
                'attribute' => 'tester',
                'label' => Yii::t('app', 'Usunął'),
                'format'=>'html'
            ],
            [
                'attribute' => 'description',
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
