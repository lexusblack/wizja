<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearConnected */


$this->title = Yii::t('app', 'Edytuj sprzęt powiązany dla: ').$model->gear->name;
$this->params['breadcrumbs'][] = ['label' => 'Model', 'url' => ['gear/view', 'id'=>$model->gear_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-connected-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
