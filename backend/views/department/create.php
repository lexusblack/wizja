<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Department */

$this->title = Yii::t('app', 'Dodaj oddział');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oddziały'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
