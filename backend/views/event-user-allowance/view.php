<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EventUserAllowance */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Diety'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-user-allowance-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'event_id',
            'user_id',
            'amount',
            'type',
            'status',
        ],
    ]) ?>

</div>
