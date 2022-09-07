<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Warehouse */

$this->title = Yii::t('app', 'Dodaj magazyn');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyny'), 'url' => ['indexw']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
