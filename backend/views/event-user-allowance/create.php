<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventUserAllowance */

$this->title = Yii::t('app', 'Dodaj dietę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Diety'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-user-allowance-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
