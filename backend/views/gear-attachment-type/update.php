<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearAttachemntType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Foldery w załącznikach sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="gear-attachemnt-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
