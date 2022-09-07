<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearsPrice */

$this->title = Yii::t('app', 'Dodaj stawkę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gears-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
