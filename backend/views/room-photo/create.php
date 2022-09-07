<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RoomPhoto */

$this->title =  Yii::t('app', 'Dodaj zdjęcie');
$this->params['breadcrumbs'][] = ['label' => $room->name, 'url' => ['location/view', 'id'=>$room->location_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-photo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
