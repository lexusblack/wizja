<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PaymentmethodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Metody płatności');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="paymentmethod-index">

    <p>
        <?php
        if ($user->can('financesPaymentMethodsCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel panel-default">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'update' => $user->can('financesPaymentMethodsUpdate'),
                        'delete' => $user->can('financesPaymentMethodsDelete'),
                        'view' => $user->can('financesPaymentMethodsView'),
                ]
            ],
        ],
    ]); ?>

    </div>
</div>