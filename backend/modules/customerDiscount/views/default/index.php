<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CustomerDiscountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rabaty');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="customer-discount-index">

    <p>
        <?php if ($user->can('clientDiscountAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'discount',
            'customersLabel:text:'.Yii::t('app', 'Klienci'),
            'categoriesLabel:text:'.Yii::t('app', 'Kategorie'),

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'view' => $user->can('clientDiscountView'),
                    'update' => $user->can('clientDiscountEdit'),
                    'delete' => $user->can('clientDiscountDelete'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>