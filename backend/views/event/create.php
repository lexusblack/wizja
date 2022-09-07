<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = Yii::t('app', 'Dodaj Event');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <?= $this->render('_form', [
        'model' => $model,
        'schema_change_possible' => $schema_change_possible,
        'event'=>$event,
        'offer'=>$offer
    ]) ?>

</div>
