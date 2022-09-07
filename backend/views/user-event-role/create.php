<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserEventRole */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Role na evencie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-event-role-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
