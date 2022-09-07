<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearCategory */

$this->title = Yii::t('app', 'Dodaj kategorię');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kategorie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
