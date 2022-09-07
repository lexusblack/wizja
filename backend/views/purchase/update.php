<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Purchase */

$this->title = Yii::t('app', 'Edycja');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zakupy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
