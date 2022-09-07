<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PriceGroup */

$this->title = Yii::t('app', 'Dodaj grupę cenową');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupy cenowe'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
