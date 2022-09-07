<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Personal */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Spotkanie prywatne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' .  Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' .  Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            'name',
            'location',
            'start_time',
            'end_time',
            'repeatLabel',
            'repeat_since',
            'reminderLabel',
            'description:ntext',
            'user.displayLabel',
            [
                'label' =>  Yii::t('app', 'Powiadomienia SMS'),
                'value' => function($model) {
                    if ($model->notificationSms) {
                        return $model->notificationSms->sending_time;
                    }
                    return "Nie";
                }
            ],
            [
                'label' =>  Yii::t('app', 'Powiadomienie Mailowe'),
                'value' => function($model) {
                    if ($model->notificationMail) {
                        return $model->notificationMail->sending_time;
                    }
                    return 'Nie';
                }
            ],
            'remind_push',
        ],
    ]) ?>
        </div>
    </div>
</div>
