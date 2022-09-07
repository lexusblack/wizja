<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Rent */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="rent-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?php

                $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                $calendarOptions = [
                    'timePicker'=>true,
                    'timePickerIncrement'=>5,
                    'timePicker24Hour' => true,
                    'locale'=>['format' => 'Y-m-d H:i'],
                    'linkedCalendars'=>false,
                ];

                echo '<label class="control-label">'.$model->getAttributeLabel('dateRange').'</label>';
                echo '<div class="input-group drp-container">';
                echo \kartik\daterange\DateRangePicker::widget([
                        'model'=>$model,
                        'attribute' => 'dateRange',
                        'useWithAddon'=>true,
                        'convertFormat'=>true,
                        'startAttribute' => 'start_time',
                        'endAttribute' => 'end_time',
                        'pluginOptions'=>$calendarOptions,
                    ]) . $addon;
                echo '</div>';
                ?>
            </div>
            <?= $form->field($model, 'days')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

        </div>
        <div class="col-md-6">
        
            <?= $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className()) ?>

            <?= $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className()) ?>
            <?php echo $form->field($model, 'manager_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_SUPERADMIN]),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php echo $form->field($model, 'description')->widget(\common\widgets\RedactorField::className()); ?>
            <?= $form->field($model, 'status')->dropDownList(\common\models\Rent::getStatusList()) ?>
                        <?php 
            if ($schema_change_possible)
            echo $form->field($model, 'tasks_schema_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TasksSchema::getList('rent'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
        </div>
    </div>






    <div class="form-group">
        <?= Html::submitButton( Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
