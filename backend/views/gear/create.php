<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = Yii::t('app', 'Dodaj model');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'modele'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-create">

    <?= $this->render('_form', [
        'model' => $model,
        'rfids' => $rfids
    ]) ?>

</div>
