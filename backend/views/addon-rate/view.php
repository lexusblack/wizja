<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AddonRate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addon-rate-view">

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
            'amount:currency',
//            'create_time',
//            'update_time',
        ],
    ]) ?>

</div>
