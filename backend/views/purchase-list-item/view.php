<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseListItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Purchase List Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-list-item-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Purchase List Item'.' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'purchaseList.name',
            'label' => 'Purchase List',
        ],
        'name',
        'quantity',
        'company_name',
        'company_address',
        [
            'attribute' => 'outerGear.id',
            'label' => 'Outer Gear',
        ],
        [
            'attribute' => 'event.name',
            'label' => 'Event',
        ],
        'status',
        'position',
        'description',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>Event<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnEvent = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'location_id',
        'customer_id',
        'contact_id',
        'manager_id',
        'info',
        'description',
        'code',
        'event_start',
        'event_end',
        'status',
        'type',
        'create_time',
        'update_time',
        'packing_start',
        'packing_end',
        'montage_start',
        'montage_end',
        'readiness_start',
        'readiness_end',
        'practice_start',
        'practice_end',
        'disassembly_start',
        'disassembly_end',
        'packing_type',
        'montage_type',
        'readiness_type',
        'practice_type',
        'disassembly_type',
        'level',
        'route_start',
        'route_end',
        'provision',
        'offer_prepared',
        'offer_sent',
        'offer_sent_date',
        'offer_sent_user_id',
        'offer_accepted',
        'ready_to_invoice',
        'ready_to_invoice_date',
        'ready_to_invoice_user_id',
        'invoice_issued',
        'invoice_sent',
        'expense_entered',
        'expense_entered_date',
        'expense_entered_user_id',
        'invoice_status',
        'expense_status',
        'project_settled',
        'project_paid',
        'expenses_paid',
        'project_done',
        'transfer_booked',
        'invoice_number',
        'creator_id',
        'provision_type',
        'finance_info',
        'invoice_id',
        'crew_working_time_changed',
        'tasks_schema_id',
        'address',
        'project_id',
        'send_reminders',
        'event_type',
        'details',
        'scenography_level',
        'number',
        'paying_date',
    ];
    echo DetailView::widget([
        'model' => $model->event,
        'attributes' => $gridColumnEvent    ]);
    ?>
    <div class="row">
        <h4>OuterGear<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnOuterGear = [
        ['attribute' => 'id', 'visible' => false],
        'quantity',
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
        'info',
        'photo',
        'create_time',
        'update_time',
        'price',
        'selling_price',
        'sort_order',
        'company_name',
        'active',
        'company_id',
        'outer_gear_model_id',
    ];
    echo DetailView::widget([
        'model' => $model->outerGear,
        'attributes' => $gridColumnOuterGear    ]);
    ?>
    <div class="row">
        <h4>PurchaseList<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnPurchaseList = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'datetime',
        'status',
    ];
    echo DetailView::widget([
        'model' => $model->purchaseList,
        'attributes' => $gridColumnPurchaseList    ]);
    ?>
</div>
