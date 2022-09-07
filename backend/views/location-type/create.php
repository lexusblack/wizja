<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationType */

$this->title = Yii::t('app', 'Stwórz typ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Typ'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
