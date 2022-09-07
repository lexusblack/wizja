<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\OfferSchemaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Schematy oferty');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-schema-index">

    <p>
        <?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
    ]); ?>

</div>
