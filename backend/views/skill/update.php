<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Skill */

$this->title = Yii::t('app', 'Edycja umiejętności').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Umiejętności'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="skill-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
