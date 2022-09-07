<?php

use yii\helpers\Html;
use backend\modules\permission\models\BasePermission;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>
                        <?php
                        $class2 = "";
                        if ($task->for_event){ 
                            $class2 = "for-event-".$task->for_event;
                        }
                        $class = "normal-element";
                        if ($task->status==10)
                            $class="success-element";
                        if ($task->status==5)
                        {
                            $class="danger-element";
                        }
                        if (($task->status==0)&&($task->datetime<date('Y-m-d')&&($task->datetime)))
                        {
                            $class="warning-element";
                        }
                        ?>    
                            <div class="row" style="margin:0">
                                
                                <div class="pull-left" style="margin-right:10px;">
                                <?php if (isset($task->creator)) { ?>
                                <img alt="image" class="img-circle img-very-small" src="<?php echo $task->creator->getUserPhotoUrl();?>" title="<?=$task->creator->first_name." ".$task->creator->last_name; ?>">
                                <?php } ?>
                                </div>
                                
                                <div class="pull-right team-members" style="margin:0px;">
                                <?php $members = $task->getAllUsers();
                                $user_num = count($members);
                                if ($user_num>0)
                                { ?>
                                   <a href="/admin/task/edit-users?id=<?=$task->id?>" class="edit-users-button"><img alt="image" class="img-circle img-very-small" src="<?php echo $members[0]->getUserPhotoUrl();?>" title="<?=$members[0]->first_name." ".$members[0]->last_name; ?>"></a>
                                <?php }  
                                if ($user_num>1)
                                { ?>
                                   <a href="/admin/task/edit-users?id=<?=$task->id?>" class="edit-users-button"><img alt="image" class="img-circle img-very-small" src="<?php echo $members[1]->getUserPhotoUrl();?>" title="<?=$members[1]->first_name." ".$members[1]->last_name; ?>"></a>
                                <?php }
                                if ($user_num>2)
                                { ?>
                                    <a href="/admin/task/edit-users?id=<?=$task->id?>" class="btn btn-default btn-circle edit-users-button" type="button">+<?=$user_num-2?> </a>
                                <?php }  ?>
                                <?=Html::a('<i class="fa fa-plus"></i> ', ['/task/edit-users', 'id'=>$task->id], ['class'=>'btn btn-default btn-circle edit-users-button'])?>
                                <?php 
                                if ($task->isMine(Yii::$app->user->id)){
                                if (($task->status==10)||($task->checkStatusForUser(Yii::$app->user->id))){ ?>
                                    <?= Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$task->id], ['class'=>'btn btn-primary btn-circle done-button']); ?>
                                <?php }else { ?>
                                    <?= Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$task->id], ['class'=>'btn btn-primary btn-circle btn-outline done-button']); ?>
                                <?php } } ?>
                                </div>
                                <?=Html::a($task->title, ['/task/view', 'id'=>$task->id], ['class'=>'show-service'])?>
                                <?php
                                $showEdit = false;
                                if ($task->event_id)
                                    {
                                        if ((Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL]))||($task->creator_id==Yii::$app->user->id)) {
                                            $showEdit = true;
                                        }else{
                                            if ((Yii::$app->user->id==$task->event->creator_id)||(Yii::$app->user->id==$task->event->manager_id))
                                            {
                                                $showEdit = true;
                                            }
                                        }
                                    }else{
                                        if ((Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL]))||($task->creator_id==Yii::$app->user->id)) {
                                            $showEdit = true;
                                    }
                                    }
                                if ($showEdit)
                                {
                                    echo Html::a('<i class="fa fa-pencil"></i> ', ['/task/update', 'id'=>$task->id], ['class'=>'edit-service-small']);
                                }    
                                ?>
                                
                                <div class="agile-detail">
                                <?php if ($task->datetime){ ?>
                                        <i class="fa fa-clock-o"></i> <?=substr($task->datetime,0,11)?>
                                <?php } 
                                if (count($task->taskAttachments)>0)
                                  $label_att = 'label-success';
                                else
                                  $label_att = '';
                                if (count($task->taskNotes)>0)
                                  $label_notes = 'label-success';
                                else
                                  $label_notes = '';
                              ?>
                                        <span class="label <?=$label_att?>"><i class="fa fa-file-o"></i> <?=count($task->taskAttachments)?></span>
                                        <span class="label <?=$label_notes?>" style="margin-left:3px;"><i class="fa fa-comments"></i> <?=count($task->taskNotes)?></span>
                                </div>
                                
                                </div>

<?php $this->registerJs('
$(".edit-service-small").click(function(e){
        $("#edit-service").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-service-small").on("contextmenu",function(){
       return false;
    });
');
?>