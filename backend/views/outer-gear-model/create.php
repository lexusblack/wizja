<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OuterGearModel */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outer-gear-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
