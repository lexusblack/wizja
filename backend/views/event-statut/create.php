<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventStatut */

$this->title = Yii::t('app', 'Dodaj status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Statusy'), 'url' => ['index', 'type'=>$model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-statut-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
