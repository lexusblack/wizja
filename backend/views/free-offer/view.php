<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\FreeOffer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Free Offer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="free-offer-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Free Offer'.' '. Html::encode($this->title) ?></h2>
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
        'start_time',
        'end_time',
        'company',
        'description:ntext',
        'city_id',
        'work_info:ntext',
        'transport_info:ntext',
        'money_info:ntext',
        'deal_type',
        'skills:ntext',
        'devices:ntext',
        'own_device:ntext',
        'user_id',
        'user_mail',
        'company_name',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
</div>
