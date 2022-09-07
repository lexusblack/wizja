<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventUserAddon */

$this->title = Yii::t('app', 'Dodaj dodatek');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-user-addon-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
