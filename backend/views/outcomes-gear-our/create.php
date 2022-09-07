<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOur */

$this->title = Yii::t('app', 'Wydania sprzętu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydania sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-gear-our-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
