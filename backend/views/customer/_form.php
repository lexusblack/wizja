<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin-bottom:20px;">
    <div class="col-md-6">

        <?= $form->field($model, 'nip')->textInput(['maxlength' => true]) ?>

        <?= Html::a(Yii::t('app', 'Pobierz dane z GUS'), '#', ['class' => 'btn btn-success', 'onclick'=>'getGus(); return false;']) ?>
        </div>
</div>
<div class="row">
    <div class="col-md-6">

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'groups')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\CustomerType::getList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true,
        ],
    ]); ?>

        <?= $form->field($model, 'country')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

        <?= $form->field($model, 'bank_account')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
        <?php
            echo $form->field($model, 'payment_days')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>0,
                ]
            ]);
            ?>

    </div>
    <div class="col-md-6">
        <?php echo $form->field($model, 'supplier')->checkbox(); ?>

        <?php echo $form->field($model, 'customer')->checkbox(); ?>


        <?php echo $form->field($model, 'logo')->hiddenInput() ?>
        <?php
        if ($model->logo)
        {
            echo Html::img($model->getLogoUrl(), ['width'=>'200']);
        }
        ?>


        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
//        'model'=>$model,
//        'attribute'=>'logo',
            'options'=>[
                'maxFiles' => 1,
            ],
            'eventHandlers' => [
                'success' => 'function(file, response) {
                $("#customer-logo").val(response.filename);

            }',
            ]
        ]); ?>

        <?= $form->field($model, 'info')->widget(\common\widgets\RedactorField::className()) ?>

    </div>
</div>




    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function getGus(){
        var nip = $("#customer-nip").val();
        $.get("/admin/customer/gus?nip="+nip, function(data){
                var customer = JSON.parse(data);
                if (customer.error=='ok')
                {
                    customer = customer.gus;
                    $("#customer-name").val(customer.name);
                    $("#customer-address").val(customer.address);
                    $("#customer-city").val(customer.city);
                    $("#customer-zip").val(customer.zip);
                }else{
                    toastr.error(customer.error);
                }
            });
        return false;
    }
</script>

<?php $this->registerJs('
    $("#customer-nip").change(function(){
        val = $(this).val();
        val = val.replace(/-/g, "");
        val = val.replace(/ /g, "");
        $(this).val(val);
    })
'); ?>
