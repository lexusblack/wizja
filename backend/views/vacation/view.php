<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Vacation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Urlopy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-view">

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
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            'user.displayLabel:text:'.Yii::t('app', 'Użytkownik'),
            'start_date',
            'end_date',
//            'day_number',
            'statusLabel',
//            'type',
            'create_time',
            'update_time',
        ],
    ]) ?>
        </div>
    </div>
</div>
