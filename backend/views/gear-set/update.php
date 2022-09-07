<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearSet */

$this->title = Yii::t('app','Edycja') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestawy urządzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'UEdycja';
?>
<div class="gear-set-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
