<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OutcomesGearOurSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wydania sprzętu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-gear-our-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Stwórz wydanie sprzętu naszego'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'outcome_id',
            'gear_id',
            'return_datetime',
            'return_user',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
