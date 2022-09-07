<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\InvoiceSerie */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-serie-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Napewno usunąć?'),
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
            [
                'attribute'=>'type',
                'value'=>$model->getTypeLabel()
            ],
            'pattern',
            'start_number',
            [
                'attribute'=>'reset_number_period',
                'value'=>$model->getResetNumberPeriodLabel(),
            ],

        ],
    ]) ?>
        </div>
    </div>
</div>
