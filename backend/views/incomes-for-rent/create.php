<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IncomesForRent */

$this->title = Yii::t('app', 'Stwórz przyjęcie dla wypożyczenia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przyjęcie z wypożyczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-for-rent-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
