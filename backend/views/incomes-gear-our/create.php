<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IncomesGearOur */

$this->title = Yii::t('app', 'Przychody sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przychody sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-gear-our-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
