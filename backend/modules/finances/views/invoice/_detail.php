<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div class="invoice-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($model->id) ?></h2>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'external_id',
        'paymentmethod',
        'paymentdate',
        'paymentstate',
        'disposaldate_format',
        'disposaldate_empty',
        'disposaldate',
        'date',
        'period',
        'total',
        'total_composed',
        'alreadypaid',
        'alreadypaid_initial',
        'remaining',
        'number',
        'day',
        'month',
        'year',
        'fullnumber',
        'semitemplatenumber',
        'type',
        'correction_type',
        'corrections',
        'currency',
        'currency_exchange',
        'currency_label',
        'currency_date',
        'price_currency_exchange',
        'good_price_group_currency_exchange',
        'template',
        'auto_send',
        'description:ntext',
        'header:ntext',
        'footer:ntext',
        'user_name',
        'schema',
        'schema_bill',
        'schema_canceled',
        'register_description:ntext',
        'netto',
        'tax',
        'signed',
        'hash',
        'warehouse_type',
        'notes',
        'documents',
        'tags',
        'price_type',
        'create_time',
        'update_time',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
</div>