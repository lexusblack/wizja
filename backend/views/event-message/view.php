<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EventMessage */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiadomości wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-message-view">

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
            'title',
            'content:ntext',
            'create_time',
            'update_time',
            'push',
            'sms',
            'email:email',
            'recipients_push:ntext',
            'recipients_sms:ntext',
            'recipients_email:ntext',
            'sent_time',
            'type',
            'status',
        ],
    ]) ?>

</div>
