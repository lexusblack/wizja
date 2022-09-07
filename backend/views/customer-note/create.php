<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerNote */

$this->title = Yii::t('app', 'Dodaj notkę');
$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/customer/view', 'id'=>$customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'event_id'=>$event_id,
        'ajax'=>$ajax
    ]) ?>

</div>
