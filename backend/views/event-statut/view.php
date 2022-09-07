<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\EventStatut */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Statut', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-statut-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Event Statut'.' '. Html::encode($this->title) ?></h2>
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
        'color',
        'position',
        'active',
        'blocks_costs',
        'blocks_working_times:datetime',
        'blocks_status_revert',
        'blocks_gear',
        'blocks_event',
        'reminder',
        'reminder_text:ntext',
        'reminder_roles',
        'reminder_users',
        'type',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
</div>
