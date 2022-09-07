<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProvisionGroup */

$this->title = Yii::t('app', 'Edytuj grupę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupy prowizyjne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
