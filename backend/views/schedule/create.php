<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Schedule */

$this->title = Yii::t('app', 'Dodaj element harmonogramu');
$this->params['breadcrumbs'][] = ['label' => 'Harmonogram', 'url' => ['index', 'id'=>$model->schedule_type_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
