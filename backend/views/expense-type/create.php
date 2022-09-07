<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ExpenseType */

$this->title = Yii::t('app', 'Dodaj typ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Typy wydatków'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
