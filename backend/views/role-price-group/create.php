<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RolePriceGroup */

$this->title = Yii::t('app', 'Dodaj stawkę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki obsługi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-price-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
