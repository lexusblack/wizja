<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOuter */

$this->title = Yii::t('app', 'Edytuj wydanie sprzętu').' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydania sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="outcomes-gear-outer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
