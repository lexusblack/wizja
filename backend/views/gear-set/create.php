<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearSet */

$this->title = Yii::t('app', 'Dodaj zestaw urządzeń');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestawy urządzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-set-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
