<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupStatut */

$this->title = Yii::t('app', 'Edytuj status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Statusy rezerwacji powierzchni'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-statut-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
