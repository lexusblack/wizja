<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearService */

$this->title = Yii::t('app', 'Dodaj serwis');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serwis sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-service-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
