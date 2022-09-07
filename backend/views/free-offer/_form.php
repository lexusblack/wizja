<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FreeOffer */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="free-offer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Tytuł ogłoszenia')]) ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Wybierz datę'),
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Wybierz datę'),
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Dokładny adres')]) ?>

    <?php echo $form->field($model, 'city_id')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\City::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
                        ],
                    ])->label(Yii::t('app', 'Najbliższe miasto')); ?>

    <?= $form->field($model, 'work_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'transport_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'money_info')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'deal_type')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\FreeDeal::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
                        ],
                    ])->label(Yii::t('app', 'Preferowany rodzaj umowy')); ?>

<?php echo $form->field($model, 'skillIds')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\FreeSkill::getList($model->skillIds),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ],
                    ])->label(Yii::t('app', 'Umiejętności'));
                    ?>
        <?php echo $form->field($model, 'deviceIds')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\FreeDevice::getList($model->deviceIds),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ],
                    ])->label(Yii::t('app', 'Obsługiwane urządzenia'));
                    ?>
        <?= $form->field($model, 'devices')->label(Yii::t('app', 'Urządzenia nieujęte na liście (oddzielone średnikiem)')); ?>

    <?= $form->field($model, 'own_device')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
