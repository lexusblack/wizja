<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VehicleModel */

$this->title = Yii::t('app', 'Edycja');
$this->params['breadcrumbs'][] = ['label' =>Yii::t('app', 'Modele pojazdów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
