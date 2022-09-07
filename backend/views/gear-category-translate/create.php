<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearCategoryTranslate */

$this->title = Yii::t('app', 'Dodaj tłumaczenie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lista tłumaczeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-category-translate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
