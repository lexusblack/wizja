<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="task-schema-form">

    <?php $form = ActiveForm::begin(['id'=>'TaskForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
<div class="row">
    <div class="col-lg-6">

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa'), 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
    <?php

    echo $form->field($model, 'department_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Department::getModelList(),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz dział')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); 

    echo $form->field($model, 'people')->textInput([
                                 'type' => 'number'
                            ]);

     echo $form->field($model, 'userIds')->widget(\kartik\widgets\Select2::className(), [
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
                'data' => ArrayHelper::map(\common\models\UserEventRole::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
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
    <div class="col-lg-6">
    <?php if ((!$model->event_id)&&(!$model->rent_id)&&(!$model->project_id))
    {
    echo $form->field($model, 'cyclic_type')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Task::getCyclicTypes(),
        'options' => ['placeholder' =>  Yii::t('app', 'Zadanie cykliczne...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    echo $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Event::getList(),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz event')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    echo $form->field($model, 'customer_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Customer::getList(),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz klienta')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);  
    }else{
        if ($model->event_id)
        {
            //if ($model->event->type!=1)
            //{
                echo $form->field($model, 'for_event')->widget(\kartik\widgets\Select2::classname(), [
                'data' => \common\models\Event::getList(),
                'options' => ['placeholder' =>  Yii::t('app', 'Wybierz event')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            //}
        }
    }
    ?>
    <?php echo $form->field($model, 'only_one')->dropDownList(['0'=>Yii::t('app', 'Każda przypisana osoba musi wykonać zadanie'), '1'=>Yii::t('app', 'Jedna przypisana osoba musi wykonać zadanie')])->label(false) ?>   
    <label class="control-label"><?= Yii::t('app', 'Początek pracy') ?></label>
            <?php 
            echo DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'from',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'autocomplete'=>"off"],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd hh:ii',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]); 
            echo $form->field($model, 'hours')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>1,
                ]
            ]);
            
            ?>
    <label class="control-label"><?= Yii::t('app', 'Deadline') ?></label>
            <?php 
            echo DateTimePicker::widget([
                'model' => $model,
                'attribute' => 'datetime',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'autocomplete'=>"off"],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd hh:ii',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]); 

            ?> 
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
    </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
if ($ajax){
if ($model->isNewRecord){
$this->registerJs("
$('#TaskForm').on('beforeSubmit', function(e) {
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
$('#TaskForm').on('beforeSubmit', function(e) {
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
}
?>