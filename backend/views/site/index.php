<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$user = Yii::$app->user;

$this->title = Yii::t('app', 'Kokpit');
Modal::begin([
    'id' => 'new-task',
    'header' => Yii::t('app', 'Dodaj zadanie'),
    'class'=> 'modal',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();

$this->registerJs('
    $(".add-task").click(function(e){
        $("#new-task").find(".modalContent").empty();
        e.preventDefault();
        $("#new-task").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');
?>
     <div class="row">
        <div class="col-md-7">

            <?php if ($user->can('cockpitToday')) { ?>
            <div class="ibox float-e-margins">
                    <div class="ibox-title black-bg">
                        <h5><?= Yii::t('app', 'Dzisiaj') ?></h5>
                        <div class="ibox-tools white">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                        </div>
                    </div>
                    <div>
                        <div class="ibox-content small-font dashboard-200">
                            <?php echo $this->render('_eventsTable', ['data'=>$dashboard->getTodayEvents()]); ?>
                        </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($user->can('cockpitEvents')) { ?>
                <div class="ibox float-e-margins">
                            <div class="ibox-title black-bg">
                                <h5><?= Yii::t('app', 'Najbliższe wydarzenia') ?></h5>
                                <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        <div>
                            <div class="ibox-content  small-font dashboard-200">
                                <?php echo $this->render('_eventsTable', ['data'=>$dashboard->getUpcomingEvents()]); ?>
                            </div>
                    </div>
                </div>
            <?php } ?>

                <?php if ($user->can('cockpitDepartmentEvents')) { ?>
                <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Najbliższe wydarzenia Twojego działu') ?></h5>
                                                        <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                            </div>
                        </div>
                        <div>
                            <div class="ibox-content  small-font dashboard-200">
                                <?php echo $this->render('_eventsTable', ['data'=>$dashboard->getDepartmentEvents()]); ?>
                            </div>
                    </div>
                </div>
                <?php } ?>
        </div>
        <div class="col-md-5">
            <?php if ($user->can('cockpitNotifications')) { ?>
            <div class="ibox float-e-margins">
                        <div class="ibox-title lazur-bg">
                            <h5><?= Yii::t('app', 'Aktualności') ?></h5>
                                                        <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                            </div>
                        </div>
                        <div>
                            <div class="ibox-content dashboard-400 small-font" style="padding-left:10px; padding-left:10px;">
                                <div class="feed-activity-list">
                                <?php foreach ($dashboard->getNews() as $m): ?>
                                    <?php if ((!$m->permission)||($user->can($m->permission))){ ?>
                                       <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">

                                            <div class="actions pull-right">
                                            <?php if ((!$m->auto)&&($m->user_id==Yii::$app->user->id)) echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                            <strong><?=$m->user->displayLabel?>: </strong><?=$m->text?>
                                            <?php if (!$m->auto) {
                                                if ($m->event_id)
                                                {
                                                    echo " ".Html::a($m->event->name, ['/event/view', 'id'=>$m->event_id]);
                                                }
                                                if ($m->project_id)
                                                {
                                                    echo " ".Html::a($m->project->name, ['/event/view', 'id'=>$m->project_id]);
                                                }
                                                }?>
                                            </br>
                                            <small class="text-navy"><?=$m->datetime?></small></br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/note/add-file', 'id'=>$m->id]); ?>
                                                <?php echo Html::a('<i class="fa fa-comment"></i> '.Yii::t('app', 'Dodaj komentarz'), ['/note/add-comment', 'id'=>$m->id], ['class'=>'pull-right add-comment']); ?>
                                            </small></br>
                                            <?php foreach ($m->noteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>

                                        </div>
                                        <div class="comment-list" id="comment-list<?=$m->id?>" style="padding-left:40px; padding-top:10px;">
                                        <?php foreach($m->notes as $n)
                                        { ?>
                                       <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$n->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">

                                            <div class="actions pull-right">
                                            <?php if ((!$n->auto)&&($n->user_id==Yii::$app->user->id)) echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/note/delete', 'id'=>$n->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                            <strong><?=$n->user->displayLabel?>: </strong><?=$n->text?>
                                            </br>
                                            <small class="text-navy"><?=$n->datetime?></small></br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/note/add-file', 'id'=>$n->id]); ?>
                                            </small></br>
                                            <?php foreach ($n->noteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>

                                        </div>
                                        </div>
                                        <?php    } ?>
                                        </div>
                                        </div>
                                        <?php } ?>
                                <?php endforeach; ?>
                                </div>
                            </div>
                    </div>
                </div>
                <?php } ?>
                <?php if ($user->can('cockpitStatus')) { ?>
                <div class="ibox float-e-margins">
                        <div class="ibox-title black-bg">
                            <h5><?= Yii::t('app', 'Status') ?></h5>
                                                        <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                            </div>
                        </div>
                        <div>
                            <div class="ibox-content dashboard-200   no-padding small-font">
                            <?php $status = $dashboard->getStatus(); ?>
                            <ul class="list-group">
                            <?php if ($user->can('menuInvoices')) { ?>
                            <li class="list-group-item ">
                                <span class="badge badge-info"><?=$status['event_expense']?></span>
                                <?=Html::a(Yii::t('app', 'Koszty niezaksięgowane'), '/admin/event-expense/index?EventExpenseSearch%5Bexpense_id%5D=1')?>
                            </li>
                            <?php } ?>
                            <?php if ($user->can('menuInvoices')) { ?>
                            
                            <li class="list-group-item">
                                <span class="badge badge-danger"><?=$status['late_invoice']?></span>
                                <?=Html::a(Yii::t('app', 'Faktury przeterminowane'), '/admin/finances/invoice/index?InvoiceSearch%5Blate%5D=1')?>
                            </li>
                            <?php } ?>
                            <?php if ($user->can('menuInvoices')) { ?>
                            <li class="list-group-item">
                                <span class="badge badge-success"><?=$status['late_expenses']?></span>
                                <?=Html::a(Yii::t('app', 'Koszty przeterminowane'), '/admin/finances/expense/index?ExpenseSearch%5Blate%5D=1')?>
                            </li>
                            <?php } ?>
                            <?php if ($user->can('gearService')) { ?>
                            <li class="list-group-item">
                                <span class="badge badge-warning"><?=$status['service']?></span>
                                <?=Html::a(Yii::t('app', 'Sprzęt na serwisie'), '/admin/gear-service/index?GearServiceSearch%5Bstatus%5D=0&page=1')?>
                            </li>
                            <?php } ?>
                                                    </ul>                           
                            </div>
                    </div>
                </div>
                <?php } ?>
        </div>
                        <?php if (false) { ?>
        <div class="col-md-3">

                <div class="ibox float-e-margins">
                        <div class="ibox-title red-bg">
                            <h5><?= Yii::t('app', 'Zadania') ?></h5>
                                    <div class="ibox-tools white">
                                    <?php if ($user->can('menuTasksAdd')) { ?>
                                    <?= Html::a(Yii::t('app', 'Zlecone'), '#', ['class'=>'white-button all-tasks']); ?> | 
                                    <?= Html::a(Yii::t('app', 'Moje'), '#', ['class'=>'white-button my-tasks']); ?> |
                                    <?=  Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/task/create'], ['class'=>'white-button add-task']); ?>
                                    <?php } ?>
                                    </div>
                        </div>
                        <div>
                            <div class="ibox-content dashboard-200   small-font" style="padding:0">
                                <ul class="sortable-list connectList agile-list ui-sortable" id="todo" style="padding:0">
                                <?php foreach ($dashboard->getTasks()->getModels() as $task): 
                                    $style= "";
                                    $style2 = "";
                                    if ($task->color){
                                        $style="background-color:".$task->color."; border-color:".$task->color.";";
                                        $style2 = "border-left: 3px solid ".$task->color;
                                    }
                                ?>
                                                                <li class="warning-element ui-sortable-handle mine-task" id="task<?=$task->id?>" style="<?=$style2?>" onclick="openTask(<?=$task->id?>)">                                                                    
                                                                <a href="#" class="check-link task-link" data-id="<?=$task->id?>">
                                                                <?php if ($task->status==10){ ?>

                                                                        <i class="fa fa-check-square"></i> </a>
                                                                    <?php }else{ ?>
                                                                        <i class="fa fa-square-o"></i> </a>
                                                                        <?php } ?>
                                                                    <strong><?=$task->title?></strong>
                                                                    <div class="agile-detail">
                                                                        <?php foreach($task->users as $tuser){ ?>
                                                                        <a href="#" class="pull-right btn btn-xs btn-warning" style="margin-right:1px; <?=$style?>" title="<?=$tuser->first_name." ".$tuser->last_name?>">
                                                                        <?php if (count($task->users)<2)
                                                                            echo $tuser->first_name." ".$tuser->last_name;
                                                                            else
                                                                               echo $tuser->getInitials();
                                                                           echo "</a>";
                                                                        } ?>
                                                                        <?php if ($task->end_time) { ?>
                                                                        <i class="fa fa-clock-o"></i> <?=substr($task->end_time, 0, 16)?>
                                                                        <?php } ?>
                                                                    </div>
                                                                </li>
                                <?php endforeach; ?>
                                <?php foreach ($dashboard->getTasksCreated() as $task): 
                                    $style= "";
                                    $style2 = "";
                                    if ($task->color){
                                        $style="background-color:".$task->color."; border-color:".$task->color.";";
                                        $style2 = "border-left: 3px solid ".$task->color.";";
                                    }
                                ?>
                                                                <li class="warning-element ui-sortable-handle created-task" id="task<?=$task->id?>" style="<?=$style2?>" onclick="openTask(<?=$task->id?>)">                                                                    
                                                                <a href="#" class="check-link task-link" data-id="<?=$task->id?>">
                                                                <?php if ($task->status==10){ ?>

                                                                        <i class="fa fa-check-square"></i> </a>
                                                                    <?php }else{ ?>
                                                                        <i class="fa fa-square-o"></i> </a>
                                                                        <?php } ?>
                                                                    <strong><?=$task->title?></strong>
                                                                    
                                                                    <div class="agile-detail">
                                                                        <?php foreach($task->users as $tuser){ ?>
                                                                        <a href="#" class="pull-right btn btn-xs btn-warning" style="margin-right:1px; <?=$style?>" title="<?=$tuser->first_name." ".$tuser->last_name?>">
                                                                        <?php if (count($task->users)<2)
                                                                            echo $tuser->first_name." ".$tuser->last_name;
                                                                            else
                                                                               echo $tuser->getInitials();
                                                                           echo "</a>";
                                                                        } ?>
                                                                        <?php if ($task->end_time) { ?>
                                                                        <i class="fa fa-clock-o"></i> <?=substr($task->end_time, 0, 16)?>
                                                                        <?php } ?>
                                                                    </div>
                                                                    
                                                                </li>
                                <?php endforeach; ?>
                            </ul>
                                
                            </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                        <div class="ibox-title yellow-bg">
                            <h5><?= Yii::t('app', 'Wiadomości') ?></h5>
                                <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                            </div>
                        </div>
                        <div>
                            <div class="ibox-content">
                                <?php echo \common\widgets\BbcWidget::widget(); ?>
                            </div>
                    </div>
                </div>
                
        </div>
        <?php } ?>
        </div>          

<?php
$this->registerJs("
    $('.task-link').click(function () {
        $.ajax({
          url: '".Url::to(['task/done'])."?id='+$(this).data( 'id' ),
        });
        return false;
    });
    ");

if ($dialog) {
 $this->registerJs("
    openMessageDialog(".$dialog.");
    ");   
}
?>
<?php
 $this->registerJs("
            $('.all-tasks').click(function (e) {
                e.preventDefault();
                $('#todo').find('li.mine-task').hide();
                $('#todo').find('li.created-task').show();
                $(this).addClass('underlined');
                $('.my-tasks').removeClass('underlined');
                return false;
        });
            $('.my-tasks').click(function (e) {
                e.preventDefault();
                $('#todo').find('li.mine-task').show();
                $('#todo').find('li.created-task').hide();
                $(this).addClass('underlined');
                $('.all-tasks').removeClass('underlined');
                return false;
        });
        $('.add-comment').click(function (e){
            e.preventDefault();
            var _this = $(this);
            $.ajax({
                url: $(this).attr('href'), 
                success: function(result){
                    _this.parent().parent().parent().find('.comment-list').append(result);
                }
            });
        });
        ");
?>
<script type="text/javascript">
    
    function openTask(id)
    {
        url = '<?=Url::to(['task/view'])?>?id='+id;
        var win = window.open(url, '_blank');
        win.focus();
    }

    function addNewTaskRow(response)
    {
        if (response.color)
        {
            style2="background-color:"+response.color+"; border-color:"+response.color+";";
            style = "border-left: 3px solid "+response.color;   
        }else{
            style2="";
            style="";
        }
        var newRow = '<li class="warning-element ui-sortable-handle created-task" id="task'+response.id+'" style="'+style+'"><a href="#" class="check-link" data-id="'+response.id+'"><i class="fa fa-square-o"></i> </a><strong>'+response.title+'</strong><div class="agile-detail">';
        for(var i=0; i<response.users.length; i++)
            newRow+='<a href="#" class="pull-right btn btn-xs btn-warning" style="'+style2+'">'+response.users[i]+'</a>'
        newRow +='<i class="fa fa-clock-o"></i>'+response.end_time+'</div></li>';
        $("#todo").append(newRow);
    }

    function reloadComments($id)
    {
        $("#comment-list"+$id).load('<?=Url::to(['note/get-comments'])?>?id='+$id);
    }
</script>
