<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventFinance */

$this->title = Yii::t('app', 'Stwórz finanse wydarzenia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Finanse wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-finance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
