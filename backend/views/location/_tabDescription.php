<?php
use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Opis'); ?></h3>


        <div id="location-description-form">
        <?php if ($model->public<2){ ?>
            <?php $form = ActiveForm::begin([
//                'type'=>ActiveForm::TYPE_HORIZONTAL,
                'action'=>['location/update', 'id'=>$model->id],
            ]); ?>

                <?php echo $form->field($model, 'description')->label(false)->widget(\common\widgets\RedactorField::className()); ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <?php } else{
                echo $model->description;
                } ?>
        </div>
</div>