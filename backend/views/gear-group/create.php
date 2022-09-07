<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearGroup */

$this->title = Yii::t('app', 'Dodaj zestaw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestaw sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-group-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
