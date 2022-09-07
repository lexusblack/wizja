<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventGearItem */

$this->title = Yii::t('app', 'Dodaj sprzęt do wydarzenia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sprzęt wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-gear-item-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
