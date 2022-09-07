<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearCompany */

$this->title = Yii::t('app', 'Dodaj producenta');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Producenci'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-company-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
