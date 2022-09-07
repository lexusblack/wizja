<?php
use yii\bootstrap\Html;
use kartik\widgets\ActiveForm;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Opis'); ?> <small><?php echo Yii::t('app', 'Wyświetlany na packliście'); ?></small></h3>

<?php if (Yii::$app->user->can('eventRentsEdit')) { ?>
        <div id="event-description-form">
            <?php $form = ActiveForm::begin([
                'action'=>['rent/update', 'id'=>$model->id],
            ]); ?>

                <?php echo $form->field($model, 'description')->widget(\common\widgets\RedactorField::className())->label(false); ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

<?php }
else {
    echo $model->description;
}
?>
</div>