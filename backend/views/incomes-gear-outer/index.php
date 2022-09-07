<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IncomesGearOuterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Przychód sprzętu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-gear-outer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Stwórz'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'income_id',
            'outer_gear_id',
            'gear_quantity',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
