<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\widgets\ColorInput;
use yii\widgets\ActiveForm;

$user = Yii::$app->user;
/* @var $model \common\models\Event; */

?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-inline']]); ?>
        <?= $form->field($projectUser, 'user_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => $model->getUserList(),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label(false); ?>
        <?= $form->field($projectUser, 'roleIds')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\UserEventRole::getModelList(),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz role')],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true
        ],
    ])->label(false); ?>
    <?php echo $form->field($projectUser, 'manager')->checkbox(); ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Dodaj'), ['class' => 'btn btn-primary btn-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <tbody>
                                                <?php foreach ($model->projectUsers as $pu){ $user = $pu->user; ?>
                                                <tr>
                                                    <td class="client-avatar"><?php echo ' <img alt="image" class="img-circle" src="'.$user->getUserPhotoUrl().'" title="'.$user->first_name." ".$user->last_name.'">'?></td>
                                                    <td><?=$user->first_name." ".$user->last_name?></td>
                                                    <td><?=$pu->getRolesLabel()?></td>
                                                    <td class="contact-type"><i class="fa fa-envelope"> </i></td>
                                                    <td><?=$user->email?></td>
                                                    <td class="contact-type"><i class="fa fa-phone"> </i></td>
                                                    <td><?=$user->phone?></td>
                                                    <td class="client-status"><?=$pu->getTaskStatus()['label']?></td>
                                                </tr>
                                                <?php } ?>

                                                
                                                </tbody>
                                            </table>
                                        </div>
</div>
