<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */

$this->title = Yii::t('app', 'Utwórz rozliczenie użytkownika');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rozliczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
