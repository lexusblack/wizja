<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Investition */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Investition', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="investition-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Investition'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'quantity',
        'price',
        'total_price',
        'vat',
        'year',
        'month',
        'section',
        [
            'attribute' => 'expense.name',
            'label' => 'Expense',
        ],
        [
            'attribute' => 'creator.username',
            'label' => 'Creator',
        ],
        'create_time',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>Expense<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnExpense = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'code',
        'unit',
        'netto',
        'brutto',
        'lumpcode',
        'type',
        'classification',
        'discount',
        'description',
        'notes',
        'documents',
        'tags',
        'create_time',
        'update_time',
        'count',
        'customer_id',
        'number',
        'paymentmethod',
        'alreadypaid',
        'alreadypaid_initial',
        'remaining',
        'payment_date',
        'paymentstate',
        'disposaldate',
        'date',
        'paymentdate',
        'currency',
        'currency_exchange',
        'currency_label',
        'currency_date',
        'price_currency_exchange',
        'good_price_group_currency_exchange',
        'expense_type',
        'vat',
        'total',
        'tax',
        'paid',
        'owner_id',
        'owner_class',
        'year',
        'month',
        'day',
        'data',
        'paymentmethod_id',
        [
            'attribute' => 'creator.username',
            'label' => 'Creator',
        ],
    ];
    echo DetailView::widget([
        'model' => $model->expense,
        'attributes' => $gridColumnExpense    ]);
    ?>
    <div class="row">
        <h4>User<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
        'auth_key',
        'password_hash',
        'password_reset_token',
        'email',
        'role',
        'status',
        'create_time',
        'update_time',
        'first_name',
        'last_name',
        'last_visit',
        'photo',
        'birth_date',
        'pesel',
        'id_card',
        'phone',
        'type',
        'rate_type',
        'rate_amount',
        'overtime_amount',
        'base_hours',
        'active',
        'visible_in_offer',
        'login_token',
        'zus_rate',
        'nfz_rate',
        'tax_rate',
        'vacation_days',
        'vacation_rate',
        'vat_rate',
        'gear_category_id',
    ];
    echo DetailView::widget([
        'model' => $model->creator,
        'attributes' => $gridColumnUser    ]);
    ?>
</div>
