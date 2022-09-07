<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Meeting */

$this->title = Yii::t('app', 'Dodaj spotkanie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Spotkanie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
