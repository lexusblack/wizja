<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */

use common\models\Vacation;
use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Event;
use kartik\dynagrid\DynaGrid;
use kartik\widgets\Select2;

$role = \common\models\UserEventRole::findOne($role_id);
$role_list = [];
foreach (\common\models\UserEventRole::find()->where(['active'=>1])->all() as $rolex) {
    $role_list[$rolex->id] = $rolex->name;
}
?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-4">
        <h3><?=Yii::t('app', 'Rezerwacja ekipy w roli: ')?></h3>
        </div>
        <div class="col-md-6">
        <?= Select2::widget([
                                'data' => $role_list,
                                'value'=>$role->id,
                                'name' => 'roles-',
                                'id' => 'select-user-evet-role-ajax',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz role...'),
                                    'id'=>'select-user-evet-role-ajax',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => false,
                                ],
                            ]); ?>

        </div>
        <div class="col-md-2">
        <?php echo Html::a(Html::icon('arrow-left').' Zapisz i zamknij', ['#'], ['class'=>'btn btn-primary pull-right close-modal']); ?>

        </div>
        </div>
        <div class="row" style="margin-top:10px; margin-bottom:10px;">
        <div class="col-md-6">
        <span style="background-color:#9bd1ff; font-weight:bold;">
            <?php 
            $data =$model->getAssignedUsersByTime();
            foreach ($data as $key =>$schedule){ 
                $schedule2 = \common\models\EventSchedule::findOne(['event_id'=>$model->id, 'name'=>$key]);
                if ($schedule2)
                {
                if (isset($schedule[$role_id]))
                {
                    $r = $schedule[$role_id];
                    echo $schedule2->name.": <span id='schedule-number".$schedule2->id."'>".$r['added']."</span>/".$r['quantity']." ";
                }else{
                    echo $schedule2->name.": <span id='schedule-number".$schedule2->id."'>0</span>/0 ";
                }
                }

            }
            ?>
        </span>
        </div>
                <div class="col-md-6">
                <span style="background-color:#1ab394; color:white;" class="label"><?=Yii::t('app', 'Wolny')?></span>
                <span style="background-color:#cc0000; color:white;" class="label"><?=Yii::t('app', 'Zajęty lub urlop')?></span>
                <span style="background-color:#e69138; color:white;" class="label"><?=Yii::t('app', 'Inny event <12h')?></span>
                <span style="background-color:#ff5722; color:white;" class="label"><?=Yii::t('app', 'Niepotwierdzony urlop')?></span>
                <span style="background-color:#23c6c8; color:white;" class="label"><?=Yii::t('app', 'Praca na tym evencie w innej roli')?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="max-height:600px; overflow-y: scroll;">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                <?php
                $columns =  [
                    [
                        'value'=>function($user) use ($model)
                        {
                            return Html::a('<i class="fa fa-calendar"></i>', ['/crew/conflict-calendar', 'user_id'=>$user->id, 'event_id'=>$model->id], ['class'=>"show-calendar-user btn btn-xs btn-default"]);
                        },
                        'format'=>'raw'
                    ],


                    'last_name',
                    'first_name',
                    [
                        'header'=>Yii::t('app', 'Umiejętności'),
                        'attribute' => 'skillId',
                        'filter' => \common\models\Skill::getModelList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            $names = $model->getSkills()->select('name')->column();

                            return implode('<br />',$names);
                        },
                        'format' => 'html',
                    ],
                    [
                        'header'=>Yii::t('app', 'Działy'),
                        'attribute' => 'departmentId',
                        'filter' => \common\models\Department::getModelList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            $names = $model->getDepartments()->select('name')->column();

                            return implode('<br />',$names);
                        },
                        'format' => 'html',
                    ],
                    [
                        'header'=>Yii::t('app', 'Typ'),
                        'attribute' => 'type',
                        'filter' => \common\models\User::getTypeList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return \common\models\User::getTypeList()[$model->type];
                        },
                        'format' => 'html',
                    ],

                    [
                        'header'=>Yii::t('app', 'Zarezerwuj'),
                        'value'=>function ($user, $key, $index, $column) use($role, $model)
                        {
                            return $user->getMangeCrewDiv($role, $model);
                        },
                        'format' => 'raw',
                    ]
                ];
                ?>

<?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'afterRow' => function($model, $key, $index, $grid)
                {
                    $content = "<div class='conflict-calendar' style='height:250px'></div>";
                    return Html::tag('tr',Html::tag('td', $content, ['colspan'=>8, 'style'=>"padding:0; background-color:white;"]), ['class'=>'event-task-details']);
                },
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'id'=>'crew-manage-grid',

        
            'toolbar' => [

                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-crew'],
        'columns' => $columns,
        
    ]); ?>


                </div>
            </div>
        </div>

    </div>

<?php

$width = 100/(count($model->eventSchedules)+1);
$width2 = count($model->eventSchedules)*70;
$header = "<div style='height:30px; min-width:".$width2."px'><div style=' height:100%;' class='manage-crew-div'></div>";
            foreach ($data as $key =>$schedule){ 
                $schedule2 = \common\models\EventSchedule::findOne(['event_id'=>$model->id, 'name'=>$key]);

                if ($schedule2)
                {
                    if ($schedule2->start_time)
                    {
                        if (isset($schedule[$role_id]))
                        {
                            $color = "red";
                            $r = $schedule[$role_id];
                            if ($r['added']>=$r['quantity']){
                                $color= "green";
                            }
                            
                            $header.= "<div style='width:".$width."%; height:100%; font-size:20px; text-align:center; color:".$color."' class='manage-crew-div'><span id='schedule-numberek".$schedule2->id."'>".$r['added']."</span>/<span id='schedule-numberek-total".$schedule2->id."'>".$r['quantity']."</span></div> ";
                        }else{
                           $header.= "<div style='width:".$width."%; height:100%; font-size:20px; text-align:center; color:blue;' class='manage-crew-div'><span id='schedule-numberek".$schedule2->id."'>0</span>/<span id='schedule-numberek-total".$schedule2->id."'>0</span></div>";
                        }
                    }

                }

            }
$header .= "</div>";
$assignUrl = Url::to(['crew/assign-user', 'id'=>$model->id,]);
$assignWholeEvent = Url::to(['crew/assign-user-to-whole-event', 'event_id'=>$model->id]);
$assignToRole = Url::to(['crew/assign-user-to-role2', 'id'=>$model->id,]);
$assignWarnings = '';
$calUrl = Url::to(['/crew/conflict-calendar', 'event_id'=>$model->id]);
$reload = Url::to(['crew/manage-ajax', 'id'=>$model->id, 'schedule'=>$schedule2->name]);

$this->registerJs('

$("#select-user-evet-role-ajax").change(function(e){
var modal = $("#ekipa_modal");
modal.find(".modalContent").empty();
    modal.find(".modalContent").load("'.$reload.'&role_id="+$(this).val());
});

$("#crew-manage-grid-filters").find("td:last").html("'.$header.'");
CrewChanged = false;
function openUserDetailsModal(event_id, user_id){
    var modal = $("#ekipa_modal");
    modal.find(".modalContent").load("'.Url::to(["planboard/user-form"]).'?event_id="+event_id+"&user_id="+user_id+"&role='.$role_id.'&in_event=1");
    modal.modal("show");
}




function assignUser(user_id, add) {
    var data = {
        itemId : user_id,
        add : add
    };
    if (!add)
        $.post("'.$assignUrl.'", data, function(response){toastr.error("'.Yii::t('app', 'Pracownik usunięty z ekipy').'");});
    else
        $.post("'.$assignUrl.'", data, function(response){toastr.success("'.Yii::t('app', 'Pracownik dodany do ekipy').'");});

}

function assignUserToWholeEvent(user_id){
    $.post("'.$assignWholeEvent.'&user_id="+user_id, function(response){toastr.success("'.Yii::t('app', 'Pracownik dodany do ekipy').'");});
    
}

$(".close-modal").click(function(e){
    e.preventDefault();
    $("#ekipa_modal").modal("hide");
})

$(":checkbox").on("change", function(e){
    e.preventDefault();
    if ($(this).hasClass("all"))
    {
        var add = $(this).prop("checked");
        $(this).parent().parent().find(":checkbox").each(function(){
            var add2 = $(this).prop("checked");
            if (add2!=add)
            {
                var user_id = $(this).data("user-id");
                var role_id = $(this).data("role-id");
                var schedule_id = $(this).data("schedule-id");

                if (($(this).hasClass("overlapping"))&&(add))
                {

                }else{
                    $(this).prop( "checked", add );
                    var number = parseInt($("#schedule-number"+schedule_id).html());
                if (add){
                    number++;
                    add=1;
                }
                else{
                    add=0;
                    number--;
                }
                        $("#schedule-number"+schedule_id).html(number);
                        $("#schedule-numberek"+schedule_id).html(number);
                        var number_total = parseInt($("#schedule-numberek-total"+schedule_id).html());
                        if (number>=number_total)
                        {
                            $("#schedule-numberek"+schedule_id).parent().css("color", "green");
                        }else{
                            $("#schedule-numberek"+schedule_id).parent().css("color", "red");
                        }
                    var data = {
                        user_id: user_id,
                        role_id: role_id,
                        schedule_id: schedule_id,
                        add: add
                    };
                    $.post("'.$assignToRole.'", data, function(response){
                        if (response.success==1)
                        {
                            toastr.success(response.message);
                        }
                        if (response.success==2)
                        {
                            toastr.error(response.message);
                        }
                        CrewChanged = true;
                    });
                }
            }

        });
    }else{

        var add = $(this).prop("checked");
        var user_id = $(this).data("user-id");
        var role_id = $(this).data("role-id");
        var schedule_id = $(this).data("schedule-id");
        var number = parseInt($("#schedule-number"+schedule_id).html());
        if (add){
            number++;
            add=1;
        }
        else{
            add=0;
            number--;
        }
        if (($(this).hasClass("overlapping"))&&(add))
        {
               toastr.error("'.Yii::t('app', 'Pracownik zajęty w tym okresie - dopasuj godziny w kalendarzu').'");
               $(this).closest("tr").next().show();
                $(this).closest("tr").next().find(".conflict-calendar").empty().load("'.$calUrl.'"+"&schedule_id="+schedule_id+"&user_id="+user_id+"&role_id="+role_id);
        }else{
                $("#schedule-number"+schedule_id).html(number);
                $("#schedule-numberek"+schedule_id).html(number);
                        var number_total = parseInt($("#schedule-numberek-total"+schedule_id).html());
                        if (number>=number_total)
                        {
                            $("#schedule-numberek"+schedule_id).parent().css("color", "green");
                        }else{
                            $("#schedule-numberek"+schedule_id).parent().css("color", "red");
                        }
            var data = {
                user_id: user_id,
                role_id: role_id,
                schedule_id: schedule_id,
                add: add
            };
            $.post("'.$assignToRole.'", data, function(response){
                if (response.success==1)
                {
                    toastr.success(response.message);
                }
                if (response.success==2)
                {
                    toastr.error(response.message);
                }
                CrewChanged = true;
            });
        }
    }


});

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}

    .manage-crew-div{float:left; border:1px solid white; padding-left:5px; min-width:62px;}

    input[type="checkbox"] {
  transform: scale(1.5);
  -ms-transform: scale(1.5);
  -webkit-transform: scale(1.5);
  -o-transform: scale(1.5);
  -moz-transform: scale(1.5);
  transform-origin: 0 0;
  -ms-transform-origin: 0 0;
  -webkit-transform-origin: 0 0;
  -o-transform-origin: 0 0;
  -moz-transform-origin: 0 0;
  margin-left:10px;
}
');

$this->registerJs('

$(".show-calendar-user").click(function(e)
{
    e.preventDefault();
    if ($(this).hasClass("opened"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().show();
        $(this).parent().parent().next().find(".conflict-calendar").empty().load($(this).attr("href"));
    }
    $(this).toggleClass("opened");

});
$(".show-calendar-user").on("contextmenu",function(){
       return false;
    });

$(document).on("pjax:success", function() {
    $(":checkbox").on("change", function(e){
    e.preventDefault();
    if ($(this).hasClass("all"))
    {
        var add = $(this).prop("checked");
        $(this).parent().parent().find(":checkbox").each(function(){
            var add2 = $(this).prop("checked");
            if (add2!=add)
            {
                var user_id = $(this).data("user-id");
                var role_id = $(this).data("role-id");
                var schedule_id = $(this).data("schedule-id");

                if (($(this).hasClass("overlapping"))&&(add))
                {

                }else{
                    $(this).prop( "checked", add );
                    var number = parseInt($("#schedule-number"+schedule_id).html());
                if (add){
                    number++;
                    add=1;
                }
                else{
                    add=0;
                    number--;
                }
                        $("#schedule-number"+schedule_id).html(number);
                        $("#schedule-numberek"+schedule_id).html(number);
                        var number_total = parseInt($("#schedule-numberek-total"+schedule_id).html());
                        if (number>=number_total)
                        {
                            $("#schedule-numberek"+schedule_id).parent().css("color", "green");
                        }else{
                            $("#schedule-numberek"+schedule_id).parent().css("color", "red");
                        }
                    var data = {
                        user_id: user_id,
                        role_id: role_id,
                        schedule_id: schedule_id,
                        add: add
                    };
                    $.post("'.$assignToRole.'", data, function(response){
                        if (response.success==1)
                        {
                            toastr.success(response.message);
                        }
                        if (response.success==2)
                        {
                            toastr.error(response.message);
                        }
                        CrewChanged = true;
                    });
                }
            }

        });
    }else{

        var add = $(this).prop("checked");
        var user_id = $(this).data("user-id");
        var role_id = $(this).data("role-id");
        var schedule_id = $(this).data("schedule-id");
        var number = parseInt($("#schedule-number"+schedule_id).html());
        if (add){
            number++;
            add=1;
        }
        else{
            add=0;
            number--;
        }
        if (($(this).hasClass("overlapping"))&&(add))
        {
               toastr.error("'.Yii::t('app', 'Pracownik zajęty w tym okresie - dopasuj godziny w kalendarzu').'");
               $(this).closest("tr").next().show();
                $(this).closest("tr").next().find(".conflict-calendar").empty().load("'.$calUrl.'"+"&schedule_id="+schedule_id+"&user_id="+user_id+"&role_id="+role_id);
        }else{
                $("#schedule-number"+schedule_id).html(number);
                $("#schedule-numberek"+schedule_id).html(number);
                        var number_total = parseInt($("#schedule-numberek-total"+schedule_id).html());
                        if (number>=number_total)
                        {
                            $("#schedule-numberek"+schedule_id).parent().css("color", "green");
                        }else{
                            $("#schedule-numberek"+schedule_id).parent().css("color", "red");
                        }
            var data = {
                user_id: user_id,
                role_id: role_id,
                schedule_id: schedule_id,
                add: add
            };
            $.post("'.$assignToRole.'", data, function(response){
                if (response.success==1)
                {
                    toastr.success(response.message);
                }
                if (response.success==2)
                {
                    toastr.error(response.message);
                }
                CrewChanged = true;
            });
        }
    }


});
$(".show-calendar-user").click(function(e)
{
    e.preventDefault();
    if ($(this).hasClass("opened"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().show();
        $(this).parent().parent().next().find(".conflict-calendar").empty().load($(this).attr("href"));
    }
    $(this).toggleClass("opened");

});
$(".show-calendar-user").on("contextmenu",function(){
       return false;
    });
});
');



