<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\VatRate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki VAT'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vat-rate-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Czy na pewno usunąć ten model?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-default">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'value',
        ],
    ]) ?>
    </div>
</div>
