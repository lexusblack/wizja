<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title =  Yii::t('app', 'Dodaj zdjęcie do').' '.$room->name;
$this->params['breadcrumbs'][] = ['label' => $room->location->name, 'url' => ['location/view', 'id'=>$room->location_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-attachment-create">

    <?= $this->render('_batchForm', [
        'model' => $model,
    ]) ?>

</div>
