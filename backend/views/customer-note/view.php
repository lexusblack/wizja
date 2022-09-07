<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerNote */

$this->title = substr($model->name, 0, 30);
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/customer/view', 'id'=>$model->customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-note-view">

</div>
