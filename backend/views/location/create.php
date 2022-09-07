<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Miejsca'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
