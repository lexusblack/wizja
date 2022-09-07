<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GearPurchase */

$this->title = Yii::t('app', 'Dodaj zakup');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zakupy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-purchase-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
