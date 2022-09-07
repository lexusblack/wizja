<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HallAudienceType */

$this->title = Yii::t('app', 'Dodaj rodzaj ustawienia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rodzaj ustawienia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-audience-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
