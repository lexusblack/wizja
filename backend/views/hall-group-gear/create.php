<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HallGroupGear */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => $model->hallGroup->name, 'url' => ['/hall-group/view', 'id'=>$model->hallGroup->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-gear-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
