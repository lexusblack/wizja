<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserNote */

$this->title = Yii::t('app', 'Edytuj notatkę');
$this->params['breadcrumbs'][] = ['label' => $model->user->displayLabel, 'url' => ['user/view', 'id'=>$model->user_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
