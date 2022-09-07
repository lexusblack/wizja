<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Hall */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Segmenty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="hall-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
