<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IncomesForRentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Przyjęcie z wypożyczenia');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-for-rent-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Stwórz przyjęcie z wypożyczenia'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'income_id',
            'rent_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
