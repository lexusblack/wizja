<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VehicleAttachment */

$this->title = Yii::t('app', 'Dodaj załącznik pojazdu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załączniki pojazdów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-attachment-create">

    <?= $this->render('_batchForm', [
        'model' => $model,
    ]) ?>

</div>
