<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sprzęt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="customer-index">
            <div class="row">
            <div class="col-md-12">
             <div class="ibox float-e-margins">
             <div class="ibox-content">
             <h2><?=Yii::t('app', 'Zaimportowany sprzęt')?></h2>
             <?php foreach ($models as $m){
                echo $m."<br/>";
             } ?>
             <h2><?=Yii::t('app', 'Niezaimportowany sprzęt')?></h2>
             <?php foreach ($modelsNot as $m){
                echo $m."<br/>";
             } ?>
            </div>
            </div>
        </div>
    </div>
</div>