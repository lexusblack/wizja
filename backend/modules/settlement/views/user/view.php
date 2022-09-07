<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rozliczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
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
            'user_id',
            'event_id',
            'year',
            'month',
            'department_data:ntext',
            'role_data:ntext',
            'working_hours_data:ntext',
            'sum',
            'status',
        ],
    ]) ?>

</div>
