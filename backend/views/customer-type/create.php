<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerType */

$this->title = 'Dodaj grupę kontrahentów';
$this->params['breadcrumbs'][] = ['label' => 'Grupy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
