<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearCategoryTranslate */

$this->title = Yii::t('app', 'Edytuj tłumaczenie') . ' ' . $model->gearCategory->name.Yii::t('app', ' na ').$model->language->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lista tłumaczeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title
?>
<div class="gear-category-translate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
