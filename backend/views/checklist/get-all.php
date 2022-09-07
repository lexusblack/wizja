<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Url;

?>
<div class="checlist-index">
    <div class="row">
        <div class="col-lg-12">
                <div class="ibox chat-view">
                    <div class="ibox-content" style="border:0">

                        <div class="row">

                            <div class="col-md-9">
                                <div class="chat-discussion" id="current-checklist" style="padding:0">

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group" style="margin-bottom:10px; margin-left: -25px;"><input type="text" class="form-control input-sm input" placeholder="<?=Yii::t('app', 'Nowa listy zadań')?>" id="addToDoList"> <span class="input-group-btn"> <button type="button" class="btn btn-white btn-sm" onclick="addList(); return false;"><i class="fa fa-plus"></i><?=Yii::t('app','Dodaj');?></button> </span></div>
                                <div class="chat-users checklist-list" style="height:360px">
                                <div class="users-list" id="todolist-all">
                                        <div class="chat-user" style="padding:4px 6px">
                                            <div class="chat-user-name" id="todolistitem-0">
                                                <?php $count = \common\models\Checklist::getNoListUndone();
                                                if ($count==0)
                                                    echo '<span class="label label-primary">0</span>';
                                                else
                                                   if ($count>=10) 
                                                        echo '<span class="label label-danger">'.$count.'</span>';
                                                    else
                                                        echo '<span class="label label-warning">'.$count.'</span>';
                                                ?>
                                            <a href="#" onclick="loadChecklist(0); return false;" data-id="0"  id="todolist0"><?=Yii::t('app','Ogólne')?></a>
                                            </div>
                                        </div>
                                        <ul class="todo-list small-list ui-sortable" id="todolist-main2">
                                        <?php foreach ($lists as $list): ?>
                                            <li  class="todo-item" draggable="true" id="todo-<?=$list->id?>">
                                            <div class="chat-user" style="padding:0">
                                                <div class="chat-user-name" id="todolistitem-<?=$list->id?>">

                                                <small class="pull-right"><?= Html::a('<i class="fa fa-trash"></i> ', ['/checklist/delete-todolist', 'id'=>$list->id], ['onclick'=>'deleteToDolist('.$list->id.'); return false;']); ?></small>
                                                <?php $count = $list->getUndone();
                                                if ($count==0)
                                                    echo '<span class="label label-primary">0</span>';
                                                else
                                                   if ($count>=10) 
                                                        echo '<span class="label label-danger">'.$count.'</span>';
                                                    else
                                                        echo '<span class="label label-warning">'.$count.'</span>';
                                                ?>
                                                <a href="#" onclick="loadChecklist(<?=$list->id?>); return false;" data-id="<?=$list->id?>"  id="todolist<?=$list->id?>"><?=$list->name?></a>
                                                </div>
                                            </div>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                </div>

                                </div>
                            </div>

                        </div>


                    </div>

                </div>
        </div>

    </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $( function() {
            $( '#todolist-main2' ).sortable({
            update: function (event, ui) {
                var data = $(this).sortable('serialize');
                
                // POST to server using $.post or $.ajax
                $.ajax({
                    data: data,
                    type: 'POST',
                    url: '<?=Url::to(['checklist/order-todolist'])?>'
                });
            }
        });
            $( '#todolist-main' ).disableSelection();
        });
    loadChecklist(<?=$session_checklist?>);
    $("#todolist-all .chat-user").droppable({
        tolerance: "pointer",
        accept: ".checklist-item",
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function(event, ui) {        
            $link = ui.draggable.find('.check-link').first();
            $list = $(this).find('a').last();
            if ($list.hasClass("active"))
            {

            }else{
                $.ajax({
                      url: '<?=Url::to(['checklist/add-task-todolist'])?>?task_id='+$link.data('id')+'&list_id='+$(this).find('a').last().data('id'),
                      success: function(response){
                        $("#checklistitem-"+response.task).remove();
                        var label = $("#todolistitem-"+response.list).find('.label').first();
                        label.empty().append(response.quantity);
                        label.removeClass('label-primary').removeClass('label-danger').removeClass('label-warning');
                        if (response.quantity>=10)
                        {
                            label.addClass('label-danger');
                        }else{
                            label.addClass('label-warning');
                        }
                        var label = $("#todolistitem-"+response.old).find('.label').first();
                        label.empty().append(response.oldquantity);
                        label.removeClass('label-primary').removeClass('label-danger').removeClass('label-warning');
                        if (response.oldquantity>=10)
                        {
                            label.addClass('label-danger');
                        }else{
                            if (response.oldquantity>0)
                                label.addClass('label-warning');
                            else
                                label.addClass('label-primary');
                        }
                      }
                });
            }
        }
    });
    });
    function loadChecklist(id)
    {
        $("#current-checklist").empty();
        $("#current-checklist").load('<?=Url::to(['checklist/load-checklist?id='])?>'+id);
        $('#todolist-all').find('a').removeClass('active');
        $('#todolist'+id).addClass('active');
    }

    function addList()
    {
        var name = $("#addToDoList").val();
        $("#addToDoList").val("");
        if (name!="")
        {
                $.ajax({
                  url: '<?=Url::to(['checklist/add-todolist'])?>?name='+name,
                  success: function(response){
                    $("#todolist-all").append('<div class="chat-user" style="padding:4px 6px"><div class="chat-user-name"  id="todolistitem-'+response.id+'" ><small class="pull-right"><a href="#" onclick="deleteToDolist('+response.id+'); return false;"><i class="fa fa-trash"></i> </a></small><span class="label label-primary">0</span><a href="#" id="todolist'+response.id+'" data-id="'+response.id+'">'+response.name+'</a></div></div>');
                    $("#todolist"+response.id).on('click', function(e){ e.preventDefault(); loadChecklist(response.id)});
                    $("#todolist"+response.id).parent().parent().droppable({
                    tolerance: "pointer",
                    accept: ".checklist-item",
                    activeClass: "ui-state-default",
                    hoverClass: "ui-state-hover",
                    drop: function(event, ui) {        
                        $link = ui.draggable.find('.check-link').first();
                        $list = $(this).find('a').last();
                        if ($list.hasClass("active"))
                        {

                        }else{
                            $.ajax({
                                  url: '<?=Url::to(['checklist/add-task-todolist'])?>?task_id='+$link.data('id')+'&list_id='+$(this).find('a').last().data('id'),
                                  success: function(response){
                                    $("#checklistitem-"+response.task).remove();
                                    var label = $("#todolistitem-"+response.list).find('.label').first();
                                    label.empty().append(response.quantity);
                                    label.removeClass('label-primary').removeClass('label-danger').removeClass('label-warning');
                                    if (response.quantity>=10)
                                    {
                                        label.addClass('label-danger');
                                    }else{
                                        label.addClass('label-warning');
                                    }
                                    var label = $("#todolistitem-"+response.old).find('.label').first();
                                    label.empty().append(response.oldquantity);
                                    label.removeClass('label-primary').removeClass('label-danger').removeClass('label-warning');
                                    if (response.oldquantity>=10)
                                    {
                                        label.addClass('label-danger');
                                    }else{
                                        if (response.oldquantity>0)
                                            label.addClass('label-warning');
                                        else
                                            label.addClass('label-primary');
                                    }
                                  }
                            });
                        }
                    }
                });
                    loadChecklist(response.id);
                  }
                });
        }
    }

    function deleteToDolist(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć?",
            icon:"warning",
          buttons: {
            cancel: "Nie",
            yes: {
              text: "Tak",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post('<?=Url::to(['checklist/delete-todolist?id='])?>'+item, data, function(response){
                        row = $('#todolistitem-'+response.id);
                        row.remove();
                        loadChecklist(0);
                    });
              break;       
          }
        });
    }
</script>