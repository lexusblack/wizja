<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Checklist */

$this->title = Yii::t('app', 'Edycja').': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Checklista'), 'url' => ['/site/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="checklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
