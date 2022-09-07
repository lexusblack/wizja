<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\datecontrol\DateControl;
?>
<div class="ibox-title newsystem-bg"><h5><?=$name?></h5><div class="ibox-tools white"><a class="white-button add-task" href="#" title="<?=Yii::t('app', 'Sortuj po statusie')?>" onclick="sortDone(); return false;"><i class="fa fa-long-arrow-down"></i> <?=Yii::t('app', 'Sortuj po statusie')?></a><a class="white-button add-task" href="#" title="<?=Yii::t('app', 'Usuń wykonane')?>" onclick="deleteDone(); return false;"><i class="fa fa-trash"></i> <?=Yii::t('app', 'Usuń wykonane')?></a></div></div>
<ul class="todo-list small-list ui-sortable" id="checklist-main">
                        <?php  
                        foreach ($all as $item){ ?>
                            <li class="checklist-item" draggable="true" id="checklistitem-<?=$item->id?>"  data-name="<?=$item->name?>" data-deadline="<?=$item->deadline?>">
                                <a href="#" class="check-link" data-id="<?=$item->id?>">
                                <?php if ($item->done){ ?>
                                    <i class="fa fa-check-square"></i> </a><span class="m-l-xs todo-completed"></span>
                                <?php }else{ ?>
                                    <i class="fa fa-square-o"></i> </a><span class="m-l-xs">
                                <?php } ?>
                                
                                <?=$item->name?>
                                    
                                <?php 
                                $today = date("Y-m-d H:i:s");
                                if ($item->deadline){
                                        if ($item->deadline<$today)
                                        { ?>
                                            <small class="label label-danger"><i class="fa fa-clock-o"></i> <?=substr($item->deadline, 0, 11)?></small>
                                        <?php }else{
                                            if (substr($item->deadline, 0, 11)==substr($today, 0, 11)){ ?>
                                                <small class="label label-primary"><i class="fa fa-clock-o"></i> <?= Yii::t('app', 'dzisiaj') ?> <?=substr($item->deadline, 11, 5)?></small>
                                            <?php }else{ ?>
                                                <small class="label label-info"><i class="fa fa-clock-o"></i> <?=substr($item->deadline, 0, 11)?></small>
                                            <?php }
                                        }
                                 } ?>    
                                </span>
                                <small class="pull-right"><?= Html::a('<i class="fa fa-pencil"></i> ', ['#'], ['onclick'=>'makeEditable('.$item->id.'); return false;']); ?><?= Html::a('<i class="fa fa-trash"></i> ', ['#'], ['onclick'=>'deleteItemTodolist('.$item->id.'); return false;']); ?></small>
                                
                            </li>
                       <?php }
                        ?>
</ul>
<div style="text-align:center">
<a class="btn btn-bitbucket" title="Dodaj pozycję" onclick="addItem(); return false;">
<i class="fa fa-plus"></i>
</a>
</div>

<script type="text/javascript">
    var sort_type = <?=$sort_type?>;
    $(document).ready(function(){
        $( function() {
            $( '#checklist-main' ).sortable({
            update: function (event, ui) {
                var data = $(this).sortable('serialize');
                
                // POST to server using $.post or $.ajax
                $.ajax({
                    data: data,
                    type: 'POST',
                    url: '<?=Url::to(['checklist/order'])?>'
                });
            }
        });
        $( '#checklist-main' ).disableSelection();
          } );
        $( '#checklist-main' ).find('.check-link').click(function () {
            var element = $(this);
                $.ajax({
                  url: '<?=Url::to(['checklist/done'])?>?id='+$(this).data( 'id' ),
                  success: function(response){
                    if (response.done==1)
                    {
                        element.empty().append('<i class="fa fa-check-square"></i>');
                        changeActiveLabel(0, <?=$id?>);
                    }else{
                        element.empty().append('<i class="fa fa-square-o"></i>');
                        changeActiveLabel(1, <?=$id?>);
                    }
                  }
                });
                return false;
        });


    });
    function deleteItemTodolist(item)
    {
        swal({
            title: "<?=Yii::t('app', 'Czy na pewno chcesz usunąć?')?>",
            icon:"warning",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post('<?=Url::to(['checklist/delete-item?id='])?>'+item, data, function(response){
                        row = $('#checklistitem-'+response.id);
                        $label = row.find('.fa-square-o');
                        if ($label.length>0)
                        {
                            changeActiveLabel(0, <?=$id?>);
                        }
                        row.remove();
                    });
              break;       
          }
        });
    }

    function deleteDone()
    {
         swal({
            title: "<?=Yii::t('app', 'Czy na pewno chcesz usunąć wszystkie wykonane zadania z tej listy?')?>",
            icon:"warning",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post('<?=Url::to(['checklist/clear-done?id='.$id])?>', data, function(response){
                        loadChecklist(<?=$id?>);
                    });
              break;       
          }
        });       
    }

    function sortDone()
    {
        $("#current-checklist").empty();
        $("#current-checklist").load('<?=Url::to(['checklist/sort-done?id='.$id])?>'+"&sort_type="+sort_type);
    }

    function addItem()
    {
        data=[];
        $.post('<?=Url::to(['checklist/add-item?id='.$id])?>', data, function(response){
                        $("#checklist-main").append('<li class="checklist-item ui-sortable-handle" draggable="true" id="checklistitem-'+response.id+'" data-deadline="" data-name="'+response.name+'"><a href="#" class="check-link" data-id="'+response.id+'"><i class="fa fa-square-o"></i></a><span class="m-l-xs todo-completed"></span>'+response.name+'<small class="pull-right"><a href="/admin/checklist/update?id='+response.id+'"><i class="fa fa-pencil"></i> </a><a href="/admin/checklist/#" onclick="deleteItemTodolist('+response.id+'); return false;"><i class="fa fa-trash"></i> </a></small></li>');
                        makeEditable(response.id);
                    });
        changeActiveLabel(1, <?=$id?>);
    }

    function makeEditable(id)
    {
            var $row = $("#checklistitem-"+id);
            $name = $row.data('name');
            $deadline = $row.data('deadline');
            $row.empty();
            $row.append('<form role="form" class="form-inline" id="form-item-'+id+'"><div class="form-group"><input type="text" placeholder="Nazwa" id="item-name" class="form-control" value="'+$name+'"/ style="width:400px"></div><div class="form-group"><input type="text" placeholder="Deadline" id="item-deadline" class="form-control date-control" value="'+$deadline+'" style="width:100px"/></div><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i></button><a href="#" onclick="deleteItemTodolist('+id+'); return false;" class="btn btn-danger"><i class="fa fa-trash"></i> </a></form>');
            $("#form-item-"+id+" .date-control").datepicker({ dateFormat: 'yy-mm-dd' });
            $("#form-item-"+id).on('submit', function(e){
                e.preventDefault();
                var formData = [];
                itemName = $(this).find("#item-name").val();
                itemDeadline = $(this).find("#item-deadline").val();
                $.post('<?=Url::to(['checklist/edit-item?id='])?>'+id, { name: itemName, time: itemDeadline }, function(response){
                    $row.empty();
                    $row.append('<a href="#" class="check-link" data-id="'+response.id+'">');
                    if (response.done==1)
                    {
                        $chack = '<i class="fa fa-check-square"></i>';
                    }else{
                        $chack = '<i class="fa fa-square-o"></i>';
                    }
                    $row.append('<a href="#" class="check-link" data-id="'+response.id+'">'+$chack+'</a><span class="m-l-xs">'+response.name+'</span>'+response.deadline_html+'<small class="pull-right"><a href="/admin/checklist/update?id='+response.id+'"><i class="fa fa-pencil"></i> </a><a href="/admin/checklist/#" onclick="deleteItemTodolist('+response.id+'); return false;"><i class="fa fa-trash"></i> </a></small>');
                });
            });
    }

    function changeActiveLabel(add, list)
    {
        var label = $("#todolistitem-"+list).find('.label').first();
        value = parseInt(label.text());
        if (add)
            value+=1;
        else
            value = value-1;
        label.empty().append(value);
        label.removeClass('label-primary').removeClass('label-danger').removeClass('label-warning');
                        if (value>=10)
                        {
                            label.addClass('label-danger');
                        }else{
                            if (value>0)
                                label.addClass('label-warning');
                            else
                                label.addClass('label-primary');
                        }
        updateMainLabel(add);
    }

    function updateMainLabel(add)
    {
        var label = $("#open-check-list").find('.badge');
        value = parseInt(label.text());
        if (add)
            value+=1;
        else
            value = value-1;
        label.empty().append(value);
    }
</script>