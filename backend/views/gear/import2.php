<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', "Import");
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sprzęt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="customer-index">
            <div class="row">
            <div class="col-md-12">
             <div class="ibox float-e-margins">
             <div class="ibox-content">
             <h2><?=Yii::t('app', 'Import sprzętu')?></h2>
                <p><?=Yii::t('app', 'Zaimportuj swój sprzęt z magazynu do systemu. Aby tego dokonać przygotuj plik .xlsx według')?>  <?=Html::a(Yii::t('app', 'schematu'),'/files/gear_warehouse.xlsx')?> <?=Yii::t('app', 'i wczytaj go ponizej.')?></p>
                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                <?= $form
                    ->field($model, 'filename')
                    ->fileInput();
                    ?>
                 <?= Html::submitButton(Yii::t('app', "Importuj"), ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
                </div>
                </div>
        </div>
    </div>
</div>