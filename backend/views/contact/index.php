<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Kontakty');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="contact-index">

    <p>
        <?php if ($user->can('clientContactsAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'last_name',
            'first_name',
            'phone',
            'email:email',
            'position',
             [
                 'attribute' => 'customer_id',
                 'value' => 'customer.name',
                 'filter' => \common\models\Customer::getList(),
             ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'view' => $user->can('clientContactsSee'),
                    'update' => $user->can('clientContactsEdit'),
                    'delete' => $user->can('clientContactsDelete'),
                ]
            ],

        ],
    ]); ?>
</div>
    </div>
</div>