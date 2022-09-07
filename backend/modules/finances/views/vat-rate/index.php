<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\VatRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stawki VAT');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="vat-rate-index">

    <p>
        <?php
        if ($user->can('financesVatRateCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel panel-default">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'value',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'delete' => $user->can('financesVatRateDelete'),
                        'update' => $user->can('financesVatRateEdit'),
                        'view' => $user->can('financesVatRateView'),
                ]
            ],
        ],
    ]); ?>
    </div>
</div>