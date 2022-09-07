<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rabat klienta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-discount-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
        <div class="ibox">
        <div class="ibox-title">
            <h5><?= Yii::t('app', 'Szczegóły') ?></h5>
            <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
            </div>
    </div>
    <div class="ibox-content">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            'id',
            'discount',
            'customersLabel:text:'.Yii::t('app', 'Klienci'),
            'categoriesLabel:text:'.Yii::t('app', 'Kategorie'),
        ],
    ]) ?>

        </div>
    </div>
</div>
