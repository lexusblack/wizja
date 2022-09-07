<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventUser */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-user-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
