<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RfidLog */

$this->title = 'Create Rfid Log';
$this->params['breadcrumbs'][] = ['label' => 'Rfid Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rfid-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
