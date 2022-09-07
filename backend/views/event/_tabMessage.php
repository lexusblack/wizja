<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
/* @var $model \common\models\Event; */
$message = $model->getMessage();
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Powiadomienia'); ?></h3>

<div class="row">
    <div class="col-md-12">
        <h4 style="color: white;"><?php echo Yii::t('app', 'Wiadomość'); ?></h4>
        <div id="event-message-form">
            <?php $form = ActiveForm::begin([
                'type'=>ActiveForm::TYPE_HORIZONTAL,
                'action' => ['event-message/send', 'id'=>$model->id],
            ]); ?>

                <?php echo $form->field($message, 'title')->textInput(['maxlenght'=>true]); ?>
                <?php echo $form->field($message, 'content')->textarea(); ?>

                <?php echo $form->field($message, 'email')->checkbox()->hint(implode('; ', $message->getEmailRecipients())); ?>
                <?php echo $form->field($message, 'sms')->checkbox()->hint(implode('; ', $message->getSmsRecipients())); ?>
                <?php //echo $form->field($message, 'push')->checkbox(); ?>


            <div class="form-group" style="margin:0; margin-bottom:60px;">
                <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4><?php echo Yii::t('app', 'Historia wiadomości'); ?></h4>
        <table class="table">
            <tr><th>Data</th><th>Do kogo?</th><th>Treść</th></tr>
            <?php 
                $messages = \common\models\EventMessage::find()->where(['event_id'=>$model->id])->all();
                foreach ($messages as $m){
                    ?>
                    <tr>
                        <td><?=$m->create_time?></td>
                        <td><?=$m->recipients_sms?><br/> <?=$m->recipients_email?></td>
                        <td><?=$m->title?><br/><?=$m->content?></td>
                    </tr>
                    <?php
                }
            ?>
        </table>
    </div>
</div>
</div>