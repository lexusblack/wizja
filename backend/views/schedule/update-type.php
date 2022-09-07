<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Schedule */

$this->title = Yii::t('app', 'Edytuj harmonogram');
$this->params['breadcrumbs'][] = ['label' => 'Harmonogram', 'url' => ['index', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_type', [
        'model' => $model,
    ]) ?>

</div>
