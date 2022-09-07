<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use backend\modules\permission\models\BasePermission;
use yii\helpers\Url;
use yii\bootstrap\Modal;
$user = Yii::$app->user;
if (isset($alone))
{
    $window = "#tab-request";
}else{
    $window = ".task-schema-details";
}
$this->registerJs('
    $(".edit-service").click(function(e){
        $("#edit-service").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-service").on("contextmenu",function(){
       return false;
    });
  $(".delete-item").on("click", function(e){
    e.preventDefault();
    deleteItem($(this));
  });
    $(".delete-item").on("contextmenu",function(){
       return false;
    });
');
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Dodaj załącznik')."</h4>",
    'id' => 'task-attachment-modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<div class="row">
<div class="ibox">
    <div class='ibox-title newsystem-bg'>
    <h5><?=$model->title?></h5>
    <div class="ibox-tools white">
    <?php 
    $showEdit = false;
    if ($model->event_id)
    {
        if ((Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL]))||($model->creator_id==Yii::$app->user->id)) {
            $showEdit = true;
        }else{
            if ((Yii::$app->user->id==$model->event->creator_id)||(Yii::$app->user->id==$model->event->manager_id))
            {
                $showEdit = true;
            }
        }
    }else{
        if ((Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL]))||($model->creator_id==Yii::$app->user->id)) {
            $showEdit = true;
    }
    }
     ?>

    <?php if ($showEdit) { ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ', ['/task/update', 'id'=>$model->id], ['class'=>'white-button edit-service']); ?>
    <?php } ?>
    <?php if ((Yii::$app->user->can('menuTasksDelete'.BasePermission::SUFFIX[BasePermission::ALL]))||($model->creator_id==Yii::$app->user->id)) { ?>
        <?= Html::a(Html::icon('trash'), ['/task/delete', 'id' => $model->id], [
                                            'class'=>'delete-item'
                                        ])
                                        ?>
    <?php } ?>
                            </div>
    </div>

<div class="ibox-content">
<div class="feed-activity-list">
<div class="feed-element">
<div class="col-lg-7">

<p><?=$model->content?></p>
<p> <?php if ($model->datetime) { $class= "";
    if (($model->status==0)&&($model->datetime<date('Y-m-d'))) { $class="label-warning";}
    echo "<strong>".Yii::t('app', 'Deadline: '); ?></strong><small class="label <?=$class?>"><i class="fa fa-clock-o"></i> <?=substr($model->datetime, 0, 11)?></small><?php } ?></p>
<?php if($model->cyclic_type){ ?><p><?=Yii::t('app', 'Zadanie cykliczne: ').$model->getCyclicLabel()?></p> <?php } ?>

</div>
<div class="col-lg-5">
<?php if (($user->can('menuTasksAdd'))&&($model->status!=10)) { ?>
<p><?= Html::a('<i class="fa fa-check"></i> '.Yii::t('app', 'Oznacz jako wykonane'), ['/task/set-done', 'id'=>$model->id], ['class'=>'set-done btn btn-xs btn-primary']); ?></p>
<?php } ?>
<?php
                    if ($model->status==10)
                    {
                        $content = '<small>'.Yii::t('app', 'Status').' - '.Yii::t('app', 'wykonane').'</small>
                                    <div class="progress progress-mini">
                                    <div style="width:100%;" class="progress-bar"></div>
                                    </div>';
                    }
                    if ($model->status==0)
                    {
                        if ($model->only_one)
                        {
                            if (($model->datetime<date('Y-m-d'))&&($model->datetime))
                            {
                                $content = '<small">'.Yii::t('app', 'Przekroczony termin').'</small>';
                            }else{
                                 $content = '<small>'.Yii::t('app', 'Status').' - '.Yii::t('app', 'niewykonane').'</small>';                               
                            }
                        }else{
                            $users = 0;
                            $done = 0;
                            foreach ($model->getAllUsers() as $team){ 
                                $status = $model->checkStatusForUser($team->id);
                                if ($status){
                                    $done++;
                                }
                                $users++;
                            }
                            if ($users)
                                $status = intval($done/$users*100);
                            else
                                $status = 0;
                            if (($model->datetime<date('Y-m-d'))&&($model->datetime))
                            {
                                $content = '<small>'.Yii::t('app', 'Zadanie wykonało').' '.$done.'/'.$users.'</small>                                    
                                    <div class="progress progress-mini">
                                    <div style="width:'.$status.'%;" class="progress-bar"></div>
                                    </div>';
                            }else{
                                 $content = '<small>'.Yii::t('app', 'Zadanie wykonało').' '.$done.'/'.$users.'</small>
                                 <div class="progress progress-mini">
                                    <div style="width:'.$status.'%;" class="progress-bar"></div>
                                    </div>';                               
                            }
                        }
                    }
    echo $content;  

?>
<p><small><?php if($model->only_one){ echo Yii::t('app', 'Jedna przypisana osoba musi wykonać zadanie');}else{echo Yii::t('app', 'Każda przypisana osoba musi wykonać zadanie'); } ?> </small></p>
</div>
</div>
<div class="feed-element">
<h5><?=Yii::t('app', 'Załączone pliki')?></h5>
<?php foreach ($model->taskAttachments as $file){ 
    $name = explode(".", $file->filename);
if (($name[1]=='jpg')||($name[1]=='png'))
{
    if (($name[1]=='jpg')||($name[1]=='png'))
    {
        $icon = '<a href="'.$file->getFileUrl().'" data-gallery=""><img class="room-photo" src="'.$file->getFileUrl().'" alt="" title="'.$file->filename.'"></a>';
        echo $icon;
    }
}
}
echo "<br/>";
foreach ($model->taskAttachments as $file){ 
    $name = explode(".", $file->filename);
if (($name[1]!='jpg')&&($name[1]!='png'))
{
    $icon = "";
    

                            if (($name[1]=='doc')||($name[1]=='docx'))
                            {
                                $icon = "<i class='fa fa-file-word-o'></i>";
                            }
                            if (($name[1]=='xls')||($name[1]=='xlsx'))
                            {
                                $icon = "<i class='fa fa-file-excel-o'></i>";
                            }
                            
                            if (($name[1]=='ppt')||($name[1]=='pptx'))
                            {
                                $icon = "<i class='fa fa-file-powerpoint-o'></i>";
                            }
                            if ($name[1]=='pdf')
                            {
                                $icon = "<i class='fa fa-file-pdf-o'></i>";
                            }
    echo $icon." ".Html::a($file->filename, $file->getFileUrl(), ['target'=>'_blank'])."<br/>";
    ?>

<?php } } ?>
<?php if ($user->can('menuTasksEdit')) { ?>
<?php 
    $file = new \common\models\TaskAttachment();
    $file->task_id = $model->id;
    $form = ActiveForm::begin(['id' => 'attachment-form'.$model->id]); ?>
    <?php echo $form->field($file, 'task_id')->hiddenInput()->label(false); ?>
    <?php echo $form->field($file, 'filename')->fileInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
    <?php } ?>
</div>
<div class="feed-element">
<div class="col-lg-6">
<h5><?=Yii::t('app', 'Przypisani do zadania')?> <?php if (isset($model->department_id)){ ?><span class="label pull-right"><?=$model->department->name?></span> <?php } ?></h5>

    <div class="team-members">
            <?php foreach ($model->getAllUsers() as $team){ 
                    $status = $model->checkStatusForUser($team->id);
                    ?>
                    <a href="#" style="position:relative;">
                    <?php if ($status) { ?>
                    <span class="badge badge-primary pull-right status-bagde"><i class="fa fa-check"></i></span>
                    <?php } ?>
                    <img alt="image" class="img-circle img-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                <?php } ?>
              </div>
<?php if ($model->roles){ ?>
    <div class="team-members">
            <?php foreach ($model->roles as $role){ ?>
                    <small class="label"><i class="fa fa-users"></i> <?=$role->name?></small>
                <?php } ?>
            </div>
   <?php }?>
</div>
<div class="col-lg-6">
<h5><?=Yii::t('app', 'Powiadomienia o wykonaniu zadania do:')?></h5>
<?php if ($model->notificationUsers){ ?>
    <div class="team-members">
            <?php foreach ($model->notificationUsers as $team){ ?>
                    <a href="#"><img alt="image" class="img-circle img-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                <?php } ?>
            </div>
   <?php }?>
<?php if ($model->notificationRoles){ ?>
    <div class="team-members">
            <?php foreach ($model->notificationRoles as $role){ ?>
                    <small class="label"><i class="fa fa-users"></i> <?=$role->name?></small>
                <?php } ?>
    </div>
   <?php }?>
   </div>
   </div>
<div class="feed-element">
<h5><?=Yii::t('app', 'Przypomnienia:')?></h5>
    <div class="task-schema-notifications">
    <ul class="list-group clear-list m-t">

                            
                                
                            

            <?php foreach ($model->taskNotifications as $notification){  
                ?>
               <li class="list-group-item"> 
               <?php if ($user->can('menuTasksEdit')) { ?>
            <div class="edit-task-notification" style="display:none">
            <?php $form = ActiveForm::begin(['id'=>$notification->id, 'class'=>'task-notification-form']); ?>
            <?=$form->field($notification, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']);?>
                <div class="row task-notification">
                <div class="col-sm-1">
                <?= $form->field($notification, 'email')->checkbox(['class'=>'task-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-1">
                <?= $form->field($notification, 'sms')->checkbox(['class'=>'task-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-1">
                <?= $form->field($notification, 'push')->checkbox(['class'=>'task-notification-form-inputs']) ?>
                </div>
                <div class="col-sm-2" style="padding:0">
                <?=$form->field($notification, 'time')->textInput([
                                 'type' => 'number', 'min'=>1, 'style'=>'padding-left:2px;', 'class'=>'task-notification-form-inputs form-control'
                            ])->label(false)?>
                </div>
                <div class="col-sm-5">
                <?php echo $form->field($notification, 'time_type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TaskNotification::getTimeTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id' =>'time_type_'.$notification->id,
                    'class'=>'task-notification-form-inputs'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(false);
            ?> 
                </div>
                <div class="col-sm-1">
                <a class="btn btn-success btn-sm save-notification" href="#"><span class="glyphicon glyphicon-save"></span></a>
                </div>
                <div class="col-sm-12 text-editor">
                <?=$form->field($notification, 'text')->textarea([
                                  'row'=>2, 'class'=>'task-notification-form-inputs form-control'
                            ])->label(false)?>
                </div>
                </div>
                <?php ActiveForm::end(); ?>
                </div>
                <?php } ?>
                <div class="notification">
                <?php if ($user->can('menuTasksEdit')) { ?>
                <a class="pull-right btn btn-xs btn-danger delete-notification" href="/admin/task/delete-notification?id=<?=$notification->id?>"><i class="fa fa-trash"></i></a>
                <a class="pull-right btn btn-xs btn-success edit-notification" href="#"><i class="fa fa-pencil"></i></a>
                <?php } ?>
                <span class="label label-primary"><i class="fa fa-clock-o"></i></span> <?=$notification->time." ".\common\models\TaskNotification::getTimeTypes()[$notification->time_type]?>

                </div>
                </li>
                <?php } ?>
        </ul>
    </div>
    <?php if ($user->can('menuTasksEdit')) { ?>
    <a class="btn btn-primary btn-sm add-notification" href="/admin/task/add-notification?id=<?=$model->id?>"><span class="fa fa-plus"></span> <?=Yii::t('app', 'Dodaj')?></a>
    <?php } ?>
   </div>
   <h5><?=Yii::t("app", "Komentarze")?></h5>
   <?php foreach ($model->taskNotes as $note){  
                ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <img alt="image" class="img-circle" src="<?php echo $note->user->getUserPhotoUrl();?>">
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right"></small>
                                            <strong><?=$note->user->displayLabel?></strong> <?=$note->text?> <br>
                                            <small class="text-muted"><?=$note->create_time?></small>
                                        </div>
                                    </div>
    <?php } ?>
    <?php $form = ActiveForm::begin(['id'=>'tasknote-form'.$model->id, 'action'=>['task/add-note', 'id'=>$model->id]]); 
    $note = new \common\models\TaskNote();
    ?>
     <?=$form->field($note, 'text')->textarea([
                                  'row'=>3, 'class'=>'form-control'
                            ])->label(false)?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Dodaj'), ['class' => 'btn btn-success submit-note']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
</div>
</div>
</div>

<?php
$this->registerJs('
$("#tasknote-form'.$model->id.'").on("beforeSubmit", function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (data) {
            $("'.$window.'").empty().load("/admin/task/view?id='.$model->id.'");
        },
        error: function () {
            alert("Something went wrong");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});


    $(".save-notification").click(function(e){
        e.preventDefault();
        $("'.$window.'").empty().load("/admin/task/view?id='.$model->id.'");
    });
    $("#taskattachment-filename").change(function(e){
        showAddAttachmentModal(e, "'.$window.'");
    });
    $(".save-notification").on("contextmenu",function(){
       return false;
    });
    $(".add-notification").click(function(e){
        e.preventDefault();
        data=[];
        $.post($(this).attr("href"), data, function(response){
                        $("'.$window.'").empty().load("/admin/task/view?id='.$model->id.'");
                    });
    });
    $(".add-notification").on("contextmenu",function(){
       return false;
    });
    $(".set-done").click(function(e){
        e.preventDefault();
        data=[];
        $.post($(this).attr("href"), data, function(response){
                        editServiceRow(response);
                    });
    });
    $(".set-done").on("contextmenu",function(){
       return false;
    });
    $(".delete-notification").click(function(e){
        e.preventDefault();
        data=[];
        $.post($(this).attr("href"), data, function(response){
                        $("'.$window.'").empty().load("/admin/task/view?id='.$model->id.'");
                    });
    });
    $(".delete-notification").on("contextmenu",function(){
       return false;
    });

    $(".edit-notification").click(function(e){
        e.preventDefault();
        $(this).parent().prev().show();
        $(this).parent().hide();
    });
    $(".edit-notification").on("contextmenu",function(){
       return false;
    });
    $(".task-notification-form-inputs").change(function(e){ 
        var form = $(this).closest("form");
        $.post("/admin/task/edit-notification?id="+form.attr("id"), form.serialize(), function(response){});
    });
');
$addAttachmentUrl = Url::to(['task-attachment/upload']);

?>

<script type="text/javascript">
    function showAddAttachmentModal(e, $window){
        var files = e.target.files;
        var data = new FormData();
        $.each(files, function(key, value)
        {
            data.append('file', value);
        });
        data.append("task_id", <?=$model->id?>);
        $.ajax({
            url: '<?=$addAttachmentUrl?>',  
            type: 'POST',
            data: data,
            success:function(data){
                $($window).empty().load("/admin/task/view?id=<?=$model->id?>");
            },
            cache: false,
            contentType: false,
            processData: false
        });
        }
</script>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>