<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */

$this->title = Yii::t('app', 'Dodaj status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Statusy serwisu sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-service-statut-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
