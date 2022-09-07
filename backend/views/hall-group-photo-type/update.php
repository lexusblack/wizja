<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupPhotoType */

$this->title = Yii::t('app', 'Edytuj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Foldery załączników'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-photo-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
