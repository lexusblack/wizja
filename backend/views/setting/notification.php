<?php
/* @var $this yii\web\View */
/* @var $model \common\models\Notification */

use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;

\common\assets\BootstrapToggleAsset::register($this);

$this->title = Yii::t('app', 'Powiadomienia');
$this->params['breadcrumbs'][] = ['label'=>Yii::t('app', 'Ustawienia'), 'url'=>['setting/index']];
$this->params['breadcrumbs'][] = $this->title;

    $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_VERTICAL,
        'fieldConfig' => [
        ],
        'formConfig' => [
            'showLabels'=>true,
        ],
    ]);
    ?>
    <?= $form->field($settingForm, 'eventNotifications')->checkbox(); ?>

<div class="settings-personalize">

    <div class="row">
        
            <?php 
            $i=0;
            foreach ($models as $index=>$model): ?>

                <?php 
                if ($i==0){ echo "</div><div class='row'>"; }
                $i++;
                if ($i==2){ $i=0;}

                ?>

                <div class="col-md-6">
               <div class="ibox float-e-margins">
                <div class="ibox-title newsystem-bg">
                        <h5><?php echo $model->label; ?></h5>
                </div>
                <div class="placeholders"><?= $model->getPlaceholders() ?></div>

                     <div class="ibox-content">
                    <p class="text-muted"><?php echo $model->hint; ?></p>
                 <fieldset>
                <?php //echo $form->field($model, "[$index]title")->textInput(['maxlength'=>true])->hint($model->hint); ?>
                <?= $form->field($model, "[$index]content")->widget(\common\widgets\RedactorField::className()); ?>
                <?= $form->field($model, "[$index]mail")->checkbox(); ?>
                <?= $form->field($model, "[$index]sms")->checkbox(); ?>
                <?= $form->field($model, "[$index]push")->checkbox(); ?>
                <?php 
                if (($model->name==\common\models\Notification::READY_TO_INVOICE)||($model->name==\common\models\Notification::COSTS_ADDED))
                {
                    echo $form->field($model, '['.$index.']userIds')->widget(\kartik\widgets\Select2::className(), [
                        'data' => \common\models\User::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ],
                    ]);                    
                }

            ?>
                </fieldset>
                </div>
                </div>
                </div>
            <?php endforeach; ?>

    </div>


    <?php if (Yii::$app->user->can('settingsNotificationsSave')) {?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>
    <?php } ?>
    <?php
    ActiveForm::end();
    ?>

</div>

<?php

$this->registerCss("
    .placeholders { background-color: orangered; color: white; padding-left: 15px; padding-right: 15px; } 
");