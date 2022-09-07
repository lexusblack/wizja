<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Firm */

$this->title = 'Dodaj';
$this->params['breadcrumbs'][] = ['label' => 'Firmy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firm-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
