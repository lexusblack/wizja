<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\InvoiceSerieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Serie faktur');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="invoice-serie-index">

    <p>
        <?php
        if ($user->can('financesInvoiceSeries2Create')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) . " ";
        }
        echo Html::a(Yii::t('app', 'Domyślne serie'), ['default-series'], ['class' => 'btn btn-success']);
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
            'name',
            [
                'attribute' => 'type',
                'value'=>'typeLabel',
                'filter' => \common\models\Invoice::getTypeList(),
            ],
            'pattern',
            'start_number',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'update' => $user->can('financesInvoiceSeries2Edit'),
                        'delete' => $user->can('financesInvoiceSeries2Delete'),
                        'view' => $user->can('financesInvoiceSeries2View'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>