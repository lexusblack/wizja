<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupNote */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Hall Group Note', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-note-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Hall Group Note'.' '. Html::encode($this->title) ?></h2>
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
        'text:ntext',
        [
            'attribute' => 'hallGroup.name',
            'label' => 'Hall Group',
        ],
        'datetime',
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
        <h4>HallGroup<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnHallGroup = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'area',
        'width',
        'length',
        'height',
        'main_photo',
        'description',
    ];
    echo DetailView::widget([
        'model' => $model->hallGroup,
        'attributes' => $gridColumnHallGroup    ]);
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
        'model' => $model->user,
        'attributes' => $gridColumnUser    ]);
    ?>
</div>
