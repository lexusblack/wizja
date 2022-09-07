<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
<?php if (($superusers>=$superusers_paid)&&(!$model->isSuperUser())){ ?>
<div class="alert alert-danger">
<?= Yii::t('app', 'Wykorzystano limit superuserów ').$superusers."/".$superusers_paid.". ".Yii::t('app', 'Dodanie kolejnego konta z uprawnieniami superuser  wiąże się z dodatkowym kosztem zgodnie z cennikiem. Koszt zostanie doliczony przy kolejnej fakturze za subskrypcje programu.') ?>
</div>
 <?php   }?>
    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'username')->hiddenInput()->label(false)?>
            <?= $form->field($model, 'email')->textInput(['autocomplete'=>"off"])->label(Yii::t('app', 'Adres e-mail/login')) ?>
            <?php echo $form->field($model, 'newPassword')->textInput() ?>
            <?php echo Html::a(Yii::t('app', 'Generuj hasło'), '#', ['onclick'=>'$("#user-newpassword").val(Math.random().toString(36).slice(-12)); return false;', 'class'=>'btn btn-primary', 'style'=>'margin-bottom:10px']); ?>
            <?php echo $form->field($model, 'send_password')->checkbox(); ?>

            <?= $form->field($model, 'first_name')->textInput(['autocomplete'=>"off"]) ?>

            <?= $form->field($model, 'last_name')->textInput(['autocomplete'=>"off"]) ?>

            
            <?= $form->field($model, 'phone')->textInput(['autocomplete'=>"off"]) ?>
            <?= $form->field($model, 'birth_date')->widget(\kartik\widgets\DatePicker::className(),[
                'options' => ['placeholder' => Yii::t('app', 'Podaj datę')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose'=>true,
                ]
            ]); ?>
            <?= $form->field($model, 'pesel')->textInput(['autocomplete'=>"off"]) ?>
            <?= $form->field($model, 'id_card')->textInput(['autocomplete'=>"off"]) ?>


        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'type')->dropDownList(\common\models\User::getTypeList()) ?>

            <?php echo $form->field($model, 'role')->dropDownList(\common\models\User::getRoleList(true)) ?>

            <?php echo $form->field($model, 'authAssigmentIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\AuthItem::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
            <?php echo $form->field($model, 'active')->dropDownList(\common\models\User::getActiveList()) ?>
            <?php echo $form->field($model, 'visible_in_offer')->dropDownList(\common\models\User::getVisibleStatusList()) ?>
            <?php echo $form->field($model, 'rate_type')->dropDownList(\common\models\User::getRateList()) ?>
            <?php
            echo $form->field($model, 'rate_amount')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

            <div id="month-rate-details">
                <?php
                echo $form->field($model, 'base_hours')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions'=> [
                        'alias'=>'integer',
                        'rightAlign'=>false,
                        'digits'=>2,
                    ]
                ]);
                ?>

            <?php
            echo $form->field($model, 'overtime_amount')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            </div>

            <?php echo $form->field($model, 'departmentIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Department::getModelList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>

            <?php echo $form->field($model, 'skillIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Skill::getModelList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>

            <?php echo $form->field($model, 'gear_category_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\GearCategory::getMainList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>

            <?php
            if ($model->getPhotoUrl())
            {
                echo Html::img($model->getPhotoUrl(), ['style'=>'width:200px', 'class'=>'thumbnail']);
            }
            ?>
            <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#'.Html::getInputId($model, 'photo').'").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('
    toggleRateType();
    
    $("#user-rate_type").on("change", toggleRateType);
    
    function toggleRateType()
    {
        var $el = $("#month-rate-details");
        if ($("#user-rate_type").val() == '.(\common\models\User::RATE_MONTH).')
        {
            $el.show();   
        }
        else
        {
            $el.hide();
        }
    }
');