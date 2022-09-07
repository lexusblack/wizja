<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HallGroupNote */

$this->title = Yii::t('app', 'Dodaj notatkę');
$this->params['breadcrumbs'][] = ['label' => $model->hallGroup->name, 'url' => ['/hall-group/view', 'id'=>$model->hall_group_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
