<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Room */

$this->title =  Yii::t('app', 'Stwórz salę');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
