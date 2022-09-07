<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="task-schema-form">

    <?php $form = ActiveForm::begin(['id'=>'TaskSchemaForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
<div class="row">
    <div class="col-lg-6">

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa'), 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'userIds')->widget(\kartik\widgets\Select2::className(), [
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
    <?php echo $form->field($model, 'teamIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => ArrayHelper::map(\common\models\Team::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>

    <?php echo $form->field($model, 'roleIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => ArrayHelper::map(\common\models\UserEventRole::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
    <?= $form->field($model, 'manager')->checkbox() ?>


    </div>
    <div class="col-lg-6">
    <?php echo $form->field($model, 'only_one')->dropDownList(['0'=>Yii::t('app', 'Każda przypisana osoba musi wykonać zadanie'), '1'=>Yii::t('app', 'Jedna przypisana osoba musi wykonać zadanie')])->label(false) ?>
                <?php echo $form->field($model, 'time_type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TaskSchema::getTimeTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>   
            <div class="row">
            <div class="col-lg-4">
            <?=$form->field($model, 'days')->textInput([
                                 'type' => 'number', 'min'=>0
                            ])?>
            </div>
            <div class="col-lg-4">
            <?=$form->field($model, 'hours')->textInput([
                                 'type' => 'number', 'min'=>0, 'max'=>23
                            ])?>
            </div>
            <div class="col-lg-4">
            <?=$form->field($model, 'minutes')->textInput([
                                 'type' => 'number', 'min'=>0, 'max'=>59
                            ])?>
            </div>
            </div> 
    <?php echo $form->field($model, 'notificationUserIds')->widget(\kartik\widgets\Select2::className(), [
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

    <?php echo $form->field($model, 'notificationRoleIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => ArrayHelper::map(\common\models\UserEventRole::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>    
            <?= $form->field($model, 'manager_notification')->checkbox() ?>    
    </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
if ($model->isNewRecord){
$this->registerJs("
$('#TaskSchemaForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            addNewRow(data);
            $('#new-service').find('.modalContent').empty();
            $('#new-service').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");
}else{
 $this->registerJs("
$('#TaskSchemaForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            editServiceRow(data);
            $('#edit-service').find('.modalContent').empty();
            $('#edit-service').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");   
}
?>