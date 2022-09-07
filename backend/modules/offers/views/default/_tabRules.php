<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\web\View;

/* @var $model \common\models\Event; */
/* @var $this \yii\web\View */
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
?>
<div class="panel-body">
            <?php $form = ActiveForm::begin([
                'action'=>['/offer/default/rules', 'id'=>$model->id],
            ]); ?>

                <?php echo $form->field($model, 'order_rules')->widget(\common\widgets\RedactorField::className())->label(false); ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
</div>