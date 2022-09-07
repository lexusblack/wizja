<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventAdditionalStatut */

$this->title = Yii::t('app', 'Dodaj nowy status');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-additional-statut-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form2', [
        'model' => $model,
    ]) ?>

</div>
