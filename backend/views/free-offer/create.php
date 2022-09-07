<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FreeOffer */

$this->title = Yii::t('app', 'Dodaj ogłoszenie');
$this->params['breadcrumbs'][] = ['label' => 'Freelancers Network', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="free-offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
