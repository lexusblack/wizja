<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Personal */

$this->title =  Yii::t('app', 'Edycja spotkania prywatnego').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Spotkanie prywatne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] =  Yii::t('app', 'Edycja');
?>
<div class="personal-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
