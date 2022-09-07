<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HallGroup */

$this->title = Yii::t('app', 'Dodaj powierzchnię');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Powierzchnie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
