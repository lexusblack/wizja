<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Ride */

$this->title = Yii::t('app', 'Edytuj');
$this->params['breadcrumbs'][] = ['label' => (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Kilometrówka') : Yii::t('app','Przejazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ride-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
