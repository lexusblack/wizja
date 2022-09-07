<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\OfferSchema */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Offer Schema', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-schema-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Offer Schema'.' '. Html::encode($this->title) ?></h2>
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
        [
            'attribute' => 'user.username',
            'label' => 'User',
        ],
        'create_time',
        'update_time',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
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
<?php
if($providerServiceCategory->totalCount){
    $gridColumnServiceCategory = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'name',
            'in_offer',
            'position',
            'create_time',
            'update_time',
            'color',
                ];
    echo Gridview::widget([
        'dataProvider' => $providerServiceCategory,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-service-category']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Service Category'),
        ],
        'export' => false,
        'columns' => $gridColumnServiceCategory
    ]);
}
?>

    </div>
</div>
