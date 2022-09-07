<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HallGroupCost */

$this->title = Yii::t('app', 'Dodaj koszt');
$this->params['breadcrumbs'][] = ['label' => $model->hallGroup->name, 'url' => ['/hall-group/view', 'id'=>$model->hall_group_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-cost-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
