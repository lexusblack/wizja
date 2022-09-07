<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vehicle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vehicle-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(\common\models\Vehicle::typeList()) ?>
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
//        'model'=>$model,
//        'attribute'=>'logo',
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

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name_in_offer')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'price_km')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cost_km')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cost_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capacity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>
    <div data-type="<?php echo \common\models\Vehicle::TYPE_FIRM; ?>" class="type-group">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'registration_number')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'vin_number')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'fuel_consumption')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-md-6">

                <label class="control-label"><?= Yii::t('app', 'Data przeglądu') ?></label>
                <?= DatePicker::widget([
                    'model' => $model,
                    'attribute' => 'inspection_date',
                    'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ],
                ]);
                ?><br/>

                <label class="control-label"><?= Yii::t('app', 'OC ważne do') ?></label>
                <?= DatePicker::widget([
                    'model' => $model,
                    'attribute' => 'oc_date',
                    'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ],
                ]);
                ?><br/>


                <?= $form->field($model, 'reminder')->dropDownList(\common\models\Vehicle::getReminderList(), ['prompt'=>Yii::t('app', 'Brak')]) ?>

                <?php echo $form->field($model, 'reminderIds')->widget(\kartik\widgets\Select2::className(), [
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
            </div>
        </div>

    </div>
    <div data-type="<?php echo \common\models\Vehicle::TYPE_RENT; ?>" class="type-group">
        <?= $form->field($model, 'price_rent')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'description')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
//                    'plugins' => ['clips', 'fontcolor','imagemanager']
                ]
            ]);?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
//                    'plugins' => ['clips', 'fontcolor','imagemanager']
                ]
            ]);?>
        </div>
    </div>




    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('
showForType();
$("#vehicle-type").on("change", showForType);

function showForType()
{
    var type = $("#vehicle-type").val();
    $(".type-group").hide();
    $(".type-group[data-type=\""+type+"\"]").show();
}
');