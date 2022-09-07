<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załącznik lokacji'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-attachment-view">

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
            'id',
            'filename',
            'extension',
            'type',
            'status',
            'content:ntext',
            'create_time',
            'update_time',
            'info:ntext',
            'location_id',
            'mime_type',
            'base_name',
        ],
    ]) ?>

</div>
    </div>
</div>