<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearItem */

$this->title = Yii::t('app', 'Dodaj egzemplarz');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Egzemplarze'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-item-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
