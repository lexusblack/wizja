<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Note */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Note', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Note'.' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'user.username',
            'label' => 'User',
        ],
        'text:ntext',
        'datetime',
        [
            'attribute' => 'event.name',
            'label' => 'Event',
        ],
        [
            'attribute' => 'rent.name',
            'label' => 'Rent',
        ],
        [
            'attribute' => 'project.name',
            'label' => 'Project',
        ],
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        [
            'attribute' => 'note.id',
            'label' => 'Note',
        ],
        'type',
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
        'project_done',
        'invoice_issued',
        'invoice_sent',
        'transfer_booked',
        'invoice_number',
        'creator_id',
        'provision_type',
        'finance_info',
        'invoice_id',
        'offer_prepared',
        'expense_status',
        'project_paid',
        'expenses_paid',
        'offer_accepted',
        'offer_sent',
        'offer_sent_user_id',
        'ready_to_invoice',
        'ready_to_invoice_user_id',
        'expense_entered',
        'expense_entered_user_id',
        'invoice_status',
        'project_settled',
        'offer_sent_date',
        'expense_entered_date',
        'ready_to_invoice_date',
        'crew_working_time_changed',
        'tasks_schema_id',
        'address',
        [
            'attribute' => 'project.name',
            'label' => 'Project',
        ],
    ];
    echo DetailView::widget([
        'model' => $model->event,
        'attributes' => $gridColumnEvent    ]);
    ?>
    <div class="row">
        <h4>Rent<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnRent = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'start_time',
        'end_time',
        'deliver_time',
        'return_time',
        'info',
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        'contact_id',
        'status',
        'type',
        'reminder',
        'description',
        'create_time',
        'update_time',
        'private_note',
        'invoice_status',
        'invoice_number',
        'payment_status',
        'code',
        'planned',
        'days',
        'manager_id',
        'tasks_schema_id',
    ];
    echo DetailView::widget([
        'model' => $model->rent,
        'attributes' => $gridColumnRent    ]);
    ?>
    <div class="row">
        <h4>Project<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnProject = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'tasks_schema_id',
        'code',
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        'contact_id',
        'start_time',
        'end_time',
        'create_time',
        'update_time',
        'creator_id',
        'description',
    ];
    echo DetailView::widget([
        'model' => $model->project,
        'attributes' => $gridColumnProject    ]);
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
    ];
    echo DetailView::widget([
        'model' => $model->user,
        'attributes' => $gridColumnUser    ]);
    ?>
    <div class="row">
        <h4>Note<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnNote = [
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute' => 'user.username',
            'label' => 'User',
        ],
        'text:ntext',
        'datetime',
        [
            'attribute' => 'event.name',
            'label' => 'Event',
        ],
        [
            'attribute' => 'rent.name',
            'label' => 'Rent',
        ],
        [
            'attribute' => 'project.name',
            'label' => 'Project',
        ],
        [
            'attribute' => 'customer.name',
            'label' => 'Customer',
        ],
        'type',
    ];
    echo DetailView::widget([
        'model' => $model->note,
        'attributes' => $gridColumnNote    ]);
    ?>
    
    <div class="row">
<?php
if($providerNote->totalCount){
    $gridColumnNote = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'user.username',
                'label' => 'User'
            ],
            'text:ntext',
            'datetime',
            [
                'attribute' => 'event.name',
                'label' => 'Event'
            ],
            [
                'attribute' => 'rent.name',
                'label' => 'Rent'
            ],
            [
                'attribute' => 'project.name',
                'label' => 'Project'
            ],
            [
                'attribute' => 'customer.name',
                'label' => 'Customer'
            ],
                        'type',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerNote,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-note']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Note'),
        ],
        'export' => false,
        'columns' => $gridColumnNote
    ]);
}
?>

    </div>
    
    <div class="row">
<?php
if($providerNoteAttachment->totalCount){
    $gridColumnNoteAttachment = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        'filename',
            'extension',
            'mime_type',
            'base_name',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerNoteAttachment,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-note-attachment']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Note Attachment'),
        ],
        'export' => false,
        'columns' => $gridColumnNoteAttachment
    ]);
}
?>

    </div>
</div>
