<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationNote */

$this->title = Yii::t('app', 'Edytuj notatkę');
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="location-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
