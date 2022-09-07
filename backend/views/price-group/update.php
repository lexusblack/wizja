<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PriceGroup */

$this->title =  $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupy cenowe'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
