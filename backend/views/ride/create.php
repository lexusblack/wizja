<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Ride */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Kilometrówka') : Yii::t('app','Przejazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ride-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
