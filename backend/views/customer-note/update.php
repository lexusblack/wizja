<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerNote */

$this->title = Yii::t('app', 'Edytuj notkę');
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/customer/view', 'id'=>$model->customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
