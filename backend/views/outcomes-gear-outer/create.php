<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOuter */

$this->title = Yii::t('app', 'Stwórz wydanie sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydania sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-gear-outer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
