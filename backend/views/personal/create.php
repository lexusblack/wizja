<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Personal */

$this->title =  Yii::t('app', 'Dodaj spotkanie');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Spotkania prywatne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
