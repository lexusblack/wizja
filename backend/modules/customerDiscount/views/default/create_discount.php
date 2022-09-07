<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */

$this->title = Yii::t('app', 'Utwórz rabat dla ').$customer->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rabaty klienta'), 'url' => ['/customer/view', 'id'=>$customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-discount-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form2', [
        'model' => $model
    ]) ?>

</div>
