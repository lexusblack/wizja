<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vehicle */

$this->title = Yii::t('app', 'Dodaj pojazd');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pojazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
