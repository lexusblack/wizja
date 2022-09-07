<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IncomesGearOuter */

$this->title = Yii::t('app', 'Stwórz przchód sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przychód sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-gear-outer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
