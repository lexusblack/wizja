<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearSimilar */

$this->title = Yii::t('app', 'Dodaj sprzęt podobny');
$this->params['breadcrumbs'][] = ['label' => 'Model', 'url' => ['gear/view', 'id'=>$model->gear_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-similar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
