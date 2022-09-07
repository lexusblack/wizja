<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RfidCommand */

$this->title = 'Create Rfid Command';
$this->params['breadcrumbs'][] = ['label' => 'Rfid Command', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rfid-command-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
