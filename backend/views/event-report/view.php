<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\EventReport */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-report-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Event Report'.' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'event.name',
            'label' => 'Event',
        ],
        [
            'attribute' => 'manager.username',
            'label' => 'Manager',
        ],
        'name',
        'code',
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        'event_start',
        'event_end',
        'status',
        'location',
        'paying_date',
        'total_value',
        'total_cost',
        'total_provision',
        'total_predicted_cost',
        'total_predicted_provision',
        [
            'attribute' => 'eventModel.name',
            'label' => 'Event Model',
        ],
        'event_type_id',
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
        'supplier',
        'customer',
        'active',
        'payment_days',
        'customer_type_id',
        'last_date',
        'next_date',
        'country',
    ];
    echo DetailView::widget([
        'model' => $model->customer,
        'attributes' => $gridColumnCustomer    ]);
    ?>
    <div class="row">
        <h4>Event<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnEvent = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'location_id',
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        'contact_id',
        [
            'attribute' => 'manager.username',
            'label' => 'Manager',
        ],
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
        'model' => $model->manager,
        'attributes' => $gridColumnUser    ]);
    ?>
    <div class="row">
        <h4>EventType<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnEventType = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'active',
    ];
    echo DetailView::widget([
        'model' => $model->eventModel,
        'attributes' => $gridColumnEventType    ]);
    ?>
</div>
