<?php
/* @var $this yii\web\View */
/* @var $model \backend\models\SettingsForm; */

use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\ColorInput;

$this->title = Yii::t('app', 'Ustawienia');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="settings-index">
    <?php
    $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_VERTICAL,
        'fieldConfig' => [
        ],
        'formConfig' => [
            'showLabels'=>true,
        ],
    ]);
    ?>
    <div class="row">
        <div class="col-md-4">
            <?php echo $form->field($model, 'companyName')->textInput(); ?>
            <?php echo $form->field($model, 'companyShortName')->textInput(); ?>
            <?php echo $form->field($model, 'companyAddress')->textInput(); ?>
            <?php echo $form->field($model, 'companyZip')->textInput(); ?>
            <?php echo $form->field($model, 'companyCity')->textInput(); ?>
            <?php echo $form->field($model, 'companyCountry')->textInput(); ?>
            
            <?php echo $form->field($model, 'companyNIP')->textInput(); ?>
            <?php echo $form->field($model, 'companyBankName')->textInput(); ?>
            <?php echo $form->field($model, 'companyBankNumber')->textInput(); ?>

            <?php echo $form->field($model, 'crewConfirm')->dropDownList([1=>Yii::t('app', 'TAK'), 2=>Yii::t('app', 'NIE')]) ?>


        </div>
        <div class="col-md-4">
            <h3><?= Yii::t('app', 'Dział handlowy') ?>:</h3>
            <?php echo $form->field($model, 'salesDepartmentPhone')->textInput(); ?>
            <?php echo $form->field($model, 'salesDepartmentEmail')->textInput(); ?>
            <h3><?= Yii::t('app', 'Cross Rental Network') ?>:</h3>
            <?php echo $form->field($model, 'crossRentalPhone')->textInput(); ?>
            <?php echo $form->field($model, 'crossRentalEmail')->textInput(); ?>
            <?php 
                echo $form->field($model, 'crossRentalUsersArray')->widget(\kartik\widgets\Select2::className(), [
                    'data' => \common\models\User::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ]);
            ?>
            <h3><?= Yii::t('app', 'Adres magazynu') ?>:</h3>
            <label><input type="checkbox" id="warehouseSameAdress"> <?=Yii::t('app', 'Taki sam jak adres siedziby')?></label>
            <?php echo $form->field($model, 'warehouseAddress')->textInput(); ?>
            <?php echo $form->field($model, 'warehouseZip')->textInput(); ?>
            <?php echo $form->field($model, 'warehouseCity')->textInput(); ?>
        </div>
        <div class="col-md-4">
            <?php
            if ($model->getPhotoUrl())
            {
                echo Html::img($model->getPhotoUrl(), ['style'=>'width:200px', 'class'=>'thumbnail', 'id' => 'companyPhotoImg']);
            }
            ?>
            <div class="form-group">
                <?php echo $form->field($model, 'footerText')->textInput(); ?>
                <?php echo $form->field($model, 'footerSize')->textInput(); ?>
                <?php echo Html::activeHiddenInput($model, 'companyLogo'); ?>
                <?php echo Html::activeLabel($model, 'companyLogo'); ?>
                <?php

                if ($user->can('settingsCompanySave')) {
                    echo devgroup\dropzone\DropZone::widget([
                        'url'=>\common\helpers\Url::to(['upload']),
                        'name'=>'file',
                        'options'=>[
                            'maxFiles' => 1,
                        ],
                        'eventHandlers' => [
                            'success' => 'function(file, response) {
                            $("#companyPhotoImg").attr("src", "/uploads/settings/"+response.filename);
                            $("#main-companylogo").val(response.filename);
                        }',
                        ]
                    ]);
                }
                echo Html::error($model, 'companyLogo'); ?>
            </div>
        </div>
    </div>


    <?php if ($user->can('settingsCompanySave')) { ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
        </div>
    <?php } ?>
    <?php
        ActiveForm::end();
    ?>
</div>


<?php
$this->registerJs('

$("#warehouseSameAdress").click(function(e){
    $("#main-warehouseaddress").val($("#main-companyaddress").val());
    $("#main-warehousecity").val($("#main-companycity").val());
    $("#main-warehousezip").val($("#main-companyzip").val());

}); 

');
?>