<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>
<div class="row">
<div class="ibox">
    <div class='ibox-title'>
    <h4><?=$model->name?></h4>
    </div>

<div class="ibox-content">
<div class="feed-activity-list">
<div class="feed-element">
<p><?=$model->description?></p>
<p><strong><?php if($model->only_one){ echo Yii::t('app', 'Jedna przypisana osoba musi wykonać zadanie');}else{echo Yii::t('app', 'Każda przypisana osoba musi wykonać zadanie'); } ?> </strong></p>
</div>
<div class="feed-element">
<h5><?=Yii::t('app', 'Przypisani do zadania')?></h5>
<?php if ($model->users){ ?>

    <div class="team-members">
            <?php foreach ($model->users as $team){ ?>
                    <a href="#"><img alt="image" class="img-circle img-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                <?php } ?>
              </div>
   <?php }?>
<?php if (($model->roles)||($model->manager)){ ?>
    <div class="team-members">
            <?php foreach ($model->roles as $role){ ?>
                    <small class="label"><i class="fa fa-users"></i> <?=$role->name?></small>
                <?php } ?>
                <?php if ($model->manager){?>
                <small class="label"><i class="fa fa-users"></i> <?=Yii::t('app', 'Project Manager')?></small>
                <?php } ?>
            </div>
   <?php }?>
</div>
<div class="feed-element">
   <h5><?=Yii::t('app', 'Termin wykonania zadania')?></h5>
   <p><?=\common\models\TaskSchema::getTimeTypes()[$model->time_type]?> 
   <?php if($model->time_type!=1) { 
    $label = "";
    $days = $model->days;
    $hours = $model->hours;
    if ($days>0)
        if ($days==1)
            $label = $days." ".Yii::t('app', 'dzień');
        else
            $label = $days." ".Yii::t('app', 'dni');
    if ($hours>0)
        if ($hours==1)
            $label = $hours." ".Yii::t('app', 'godzinę');
        else
            if ($hours<5)
                $label .= $hours." ".Yii::t('app', 'godziny');
            else
                $label .= $hours." ".Yii::t('app', 'godzin');
    ?><small class="label"><i class="fa fa-clock-o"></i> <?=$label?></small><?php } ?>
   </p>
   </div>
   <div class="feed-element">
<h5><?=Yii::t('app', 'Powiadomienia o wykonaniu zadania do:')?></h5>
<?php if ($model->notificationUsers){ ?>
    <div class="team-members">
            <?php foreach ($model->notificationUsers as $team){ ?>
                    <a href="#"><img alt="image" class="img-circle img-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                <?php } ?>
            </div>
   <?php }?>
<?php if (($model->notificationRoles)||($model->manager_notification)){ ?>
    <div class="team-members">
            <?php foreach ($model->notificationRoles as $role){ ?>
                    <small class="label"><i class="fa fa-users"></i> <?=$role->name?></small>
                <?php } ?>
                <?php if ($model->manager_notification){?>
                <small class="label"><i class="fa fa-users"></i> <?=Yii::t('app', 'Project Manager')?></small>
                <?php } ?>
    </div>
   <?php }?>
   </div>
<div class="feed-element">
<h5><?=Yii::t('app', 'Przypomnienia:')?></h5>
    <div class="task-schema-notifications">
            <?php foreach ($model->taskSchemaNotifications as $notification){  ?>
            <?php $form = ActiveForm::begin(['id'=>$notification->id, 'class'=>'task-schema-notification-form']); ?>
            <?=$form->field($notification, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']);?>
                <div class="row task-schema-notification">
                <div class="col-sm-1">
                <?= $form->field($notification, 'email')->checkbox(['class'=>'task-schema-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-1">
                <?= $form->field($notification, 'sms')->checkbox(['class'=>'task-schema-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-1">
                <?= $form->field($notification, 'push')->checkbox(['class'=>'task-schema-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-1" style="padding:0">
                <?=$form->field($notification, 'time')->textInput([
                                 'type' => 'number', 'min'=>1, 'style'=>'padding-left:2px;', 'class'=>'task-schema-notification-form-inputs form-control'
                            ])->label(false)?>
                </div>
                <div class="col-sm-5">
                <?php echo $form->field($notification, 'time_type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TaskSchemaNotification::getTimeTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id' =>'time_type_'.$notification->id,
                    'class'=>'task-schema-notification-form-inputs'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(false);
            ?> 
                </div>
                <div class="col-sm-1">
                <a class="btn btn-primary edit-text" href="#"><span class="glyphicon glyphicon-comment"></span></a>
                </div>
                <div class="col-sm-1">

                <a class="btn btn-danger delete-notification" href="/admin/task-schema/delete-notification?id=<?=$notification->id?>"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
                <div class="col-sm-12 text-editor" style="display:none">
                <?=$form->field($notification, 'text')->textarea([
                                  'row'=>2, 'class'=>'task-schema-notification-form-inputs form-control'
                            ])->label(false)?>
                </div>
                </div>
                <?php ActiveForm::end(); ?>
                <?php } ?>
    </div>
    <a class="btn btn-primary btn-sm add-notification" href="/admin/task-schema/add-notification?id=<?=$model->id?>"><span class="fa fa-plus"></span> <?=Yii::t('app', 'Dodaj')?></a>
   </div>
</div>
</div>
</div>
</div>

<?php
$this->registerJs('
    $(".add-notification").click(function(e){
        e.preventDefault();
        data=[];
        $.post($(this).attr("href"), data, function(response){
                        $(".task-schema-details").empty().load("/admin/task-schema/view?id='.$model->id.'");
                    });
    });

    $(".delete-notification").click(function(e){
        e.preventDefault();
        data=[];
        $.post($(this).attr("href"), data, function(response){
                        $(".task-schema-details").empty().load("/admin/task-schema/view?id='.$model->id.'");
                    });
    });

    $(".edit-text").click(function(e){
        if ($(this).hasClass("btn-primary")) {
            $(this).parent().next().next().slideDown();
        }
        else {
            $(this).parent().next().next().slideUp();
        }
        $(this).toggleClass("btn-primary");
        $(this).toggleClass("btn-success");
    });
    $(".task-schema-notification-form-inputs").change(function(e){ 
        var form = $(this).closest("form");
        $.post("/admin/task-schema/edit-notification?id="+form.attr("id"), form.serialize(), function(response){});
    });
');
?>
