<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationPlan */

$this->title = Yii::t('app', 'Dodaj plan techniczny');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plan techniczny'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-plan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
