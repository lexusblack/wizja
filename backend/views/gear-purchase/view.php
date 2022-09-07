<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearPurchase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gear Purchase', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-purchase-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Gear Purchase'.' '. Html::encode($this->title) ?></h2>
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
        [
            'attribute' => 'gear.name',
            'label' => 'Gear',
        ],
        'quantity',
        'price',
        'total_price',
        'datetime',
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        [
            'attribute' => 'expense.name',
            'label' => 'Expense',
        ],
        [
            'attribute' => 'user.username',
            'label' => 'User',
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>Customer<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnCustomer = [
        ['attribute' => 'id', 'visible' => false],
        'company',
        'name',
        'address',
        'city',
        'zip',
        'phone',
        'email',
        'info',
        'create_time',
        'update_time',
        'type',
        'status',
        'logo',
        'nip',
        'bank_account',
        'customer',
        'supplier',
        'active',
    ];
    echo DetailView::widget([
        'model' => $model->customer,
        'attributes' => $gridColumnCustomer    ]);
    ?>
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
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
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
        'event_id',
        'year',
        'month',
        'day',
        'paymentmethod_id',
        'creator_id',
        'data',
    ];
    echo DetailView::widget([
        'model' => $model->expense,
        'attributes' => $gridColumnExpense    ]);
    ?>
    <div class="row">
        <h4>Gear<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnGear = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'quantity',
        'available',
        'brightness',
        'power_consumption',
        'status',
        'type',
        'category_id',
        'width',
        'height',
        'volume',
        'depth',
        'weight',
        'weight_case',
        'info',
        'photo',
        'group_id',
        'create_time',
        'update_time',
        'price',
        'no_items',
        'sort_order',
        'active',
        'visible_in_offer',
        'visible_in_warehouse',
        'value',
        'location',
        'warehouse',
        'info2',
        'unit',
        'min_quantity',
        'max_quantity',
    ];
    echo DetailView::widget([
        'model' => $model->gear,
        'attributes' => $gridColumnGear    ]);
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
    ];
    echo DetailView::widget([
        'model' => $model->user,
        'attributes' => $gridColumnUser    ]);
    ?>
</div>
