<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearsPrice */

$this->title = Yii::t('app', 'Edycja') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gears-price-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-warning">
        UWAGA!!! Zmiany procenta dnia pierwszego spowodują zmiany cen we wszystkich przeszłych ofertach.<br/>
        Aby zmienić % dnia pierwszego należy zduplikować tę stawkę i zmiany dokonać w kopii.
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
