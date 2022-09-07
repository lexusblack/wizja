<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Klienci'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="customer-index">
            <div class="row">
            <div class="col-md-12">
             <div class="ibox float-e-margins">
             <div class="ibox-content">
             <h2><?=Yii::t('app', 'Zaimportowani kontrahenci')?></h2>
             <?php foreach ($models as $m){
                echo $m."<br/>";
             } ?>
             <?php if (count($modelsNot)>0) { ?>
             <h2><?=Yii::t('app', 'Import z błędem')?></h2>
             <?php foreach ($modelsNot as $m){
                echo $m."<br/>";
             } ?>
             <?php } ?>
            </div>
            </div>
        </div>
    </div>
</div>