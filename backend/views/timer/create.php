<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Timer */

$this->title = Yii::t('app', 'Stwórz timer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Timer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
