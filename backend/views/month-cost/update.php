<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MonthCost */

$this->title = Yii::t('app', 'Edycja');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Koszty miesięczne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="month-cost-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
