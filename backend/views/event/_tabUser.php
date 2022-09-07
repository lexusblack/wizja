<?php
use common\models\EventUserPlannedWrokingTime;
use common\models\Event;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;

use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;
\common\assets\FullcalendarSchedulerAsset::register($this);
$selectTime = Html::dropDownList('selectWorkingTime', 1, [1=>Yii::t('app', 'Wszystkie'), 2=>Yii::t('app', 'Pakowanie'), 3=>Yii::t('app', 'Montaż'), 4=>Yii::t('app', 'Event'), 5=>Yii::t('app', 'Demontaż')], ['id'=>'selectWorkingTime', 'style'=>"margin-left:10px"]);
/* @var $model \common\models\Event; */

$user = Yii::$app->user;
$settings = \common\models\Settings::find()->indexBy('key')->all(); 

?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Ekipa'); ?></h3>
<div class="row">
    <div class="col-md-9">
        <?php
        if ($user->can('eventsEventEditEyeCrewManage')) {
            echo Html::a(Yii::t('app', 'Skopiuj zapotrzebowanie z ofert'), ['event/copy-crew-from-offer', 'id' => $model->id], ['class' => 'btn btn-primary']) . " ";
            echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj etap'), ['/event/add-schedule', 'id' => $model->id], [
                                                            'class' => 'btn  btn-success add-schedule2',
                                                            
                                                        ])." ";
                                                        
            echo Html::a(Yii::t('app', 'Zarządzaj'), ['crew/manage', 'id' => $model->id], ['class' => 'btn btn-success']) . " ";
            echo " ".Html::a(Yii::t('app', 'Skopiuj z'), ['event/copy-from', 'id' => $model->id, 'type' => 'crew'], ['class' => 'btn btn-info copy-modal-crew']);
            echo Html::a(Yii::t('app', 'Wyślij powiadomienia teraz'), ['site/send-reminders-event', 'id' => $model->id], ['class' => 'btn btn-success send-reminders']) . " ";
        }
        ?>
        
    </div>
     <div class="col-md-3">
     <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->field($model, 'send_reminders')->checkbox(['id'=>'sendreminderscheckbox']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php if ((!\common\models\EventOfferRole::find()->where(['event_id'=>$model->id])->count())&&(count($model->getPlanningOffers()))){
 ?>
 <div class="row" style="margin-top:50px; margin-bottom:50px">
 <div class="col-md-12" style="text-align:center;">
 <?php 
echo Html::a(Yii::t('app', 'Skopiuj zapotrzebowanie z ofert'), ['event/copy-crew-from-offer', 'id' => $model->id], ['class' => 'btn btn-primary']) . " ";
 ?>
 </div>
 </div>
 <?php   
}
?>
            <?php 
            $periods = $model->getCurrentUserWork(); 
            if ($periods) {?>
            <div class="row" style="margin-bottom:50px;">
                
            <div class="col-md-2">

            </div>
            <div class="col-md-8">
                        <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <?=Yii::t('app', 'Twoje godziny pracy:')?>
                                        </div>
                                        <div class="panel-body">
                                            <p><?php foreach ($periods as $r){ 
                                        echo "<strong>".$r->userEventRole->name."</strong>: od ".$r->working->start_time." do ".$r->working->end_time."<br/>";
                                        }?></p>
                                        </div>
                                    </div>
            
            </div>
            </div>
            <?php } ?>

            <?php 
            $users = $model->getUsersNoRole(); 
            if ($users) {?>
            <div class="row" style="margin-bottom:50px;">
            <div class="col-md-2">
            <h4><?=Yii::t('app', 'Pracownicy bez przypisanej roli:')?>
            </h4>
            </div>
            <div class="col-md-10">
                <div class="team-members">
                <?php foreach ($users as $user){  $team = $user->user; ?>
                <a href="#"   data-userid="<?=$team->id?>"  class="edit_user" style="position:relative;"><img alt="image" class="img-circle img-very-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                <?php } ?>
                </div>
            </div>
            </div>
            <?php } ?>

<div class="row">
    <div class="col-md-12">
                    <?php $data =$model->getAssignedUsersByTime(); ?>
            <div id="vertical-timeline2" class="vertical-container dark-timeline" style="width:100%; max-width:100%;">
                            <?php $last_key = ""; foreach ($data as $key =>$schedule){ $schedule2 = \common\models\EventSchedule::findOne(['event_id'=>$model->id, 'name'=>$key]); $cl = "";?>
                            <div class="vertical-timeline-block">
                                <div class="vertical-timeline-icon navy-bg" style="left:200px;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <span class="vertical-date">
                                        <h3><?=$key?>
                                                        <?php if ($schedule2){
                                                        /* ?>
                                                        <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', ['/event/update-schedule', 'id' => $schedule2->id], [
                                                            'class' => 'btn btn-xs  add-schedule2',
                                                            
                                                        ])
                                                        ?>
                                                        <?= Html::a('<i class="fa fa-trash"></i>', ['/event/delete-schedule', 'id' => $schedule2->id], [
                                                            'class' => 'btn btn-danger btn-xs',
                                                            'data' => [
                                                                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                                                'method' => 'post',
                                                            ],
                                                        ])
                                                        ?>
                                                        </div>
                                                        <?php */}else{ ?>
                                                        <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-plus"></i>', ['/event/add-schedule', 'id' => $model->id, 'name'=>$key], [
                                                            'class' => 'btn btn-xs  add-schedule2',
                                                            
                                                        ])
                                                        ?>
                                                        </div>
                                                        <?php    } ?>
                                
                                        </h3>
                                        <?php if ($schedule2){ ?>
                                        <span><?=substr($schedule2->start_time,0,10);?> - <?=substr($schedule2->end_time,0,10);?></span><br/>
                                        <small><?=substr($schedule2->start_time,11,5);?> - <?=substr($schedule2->end_time,11,5);?></small>
                                        <?php if (!$schedule2->start_time){ $cl=" no-time"; } ?>

                                         <?php }else{ $cl=" no-time";} ?> 
                                         <?php if (Yii::$app->user->can('eventsEventEditEyeCrewManage')) { ?>
                                                                         <p><?=Html::a("<i class='fa fa-plus'></i> ".Yii::t('app', 'Dodaj'), ['crew/add-event-role', 'id'=>$model->id, 'schedule'=>$key], ['class'=>'btn btn-success btn-xs add-event-role'])?> <?=Html::a("<i class='fa fa-copy'></i> ".Yii::t('app', 'Skopiuj'), ['crew/copy-event-role', 'id'=>$model->id, 'schedule'=>$key, 'prev'=>$last_key], ['class'=>'btn btn-success btn-xs copy-event-role'])?></p>
                                        <?php } ?>
                                </span>
                                <?php 
                                $last_key = $key;
                                if (!count($schedule))
                                { ?>
                                    <div class="vertical-timeline-content" style="margin-left:250px; min-height:100px;">
                                    </div>
                                <?php }
                                foreach ($schedule as $index => $role){ 
                                    if ($role['added']>=$role['quantity'])
                                    {
                                        $class="text-info";
                                    }else{
                                        $class="text-danger";
                                    }
                                    ?>
                                <div class="vertical-timeline-content" style="margin-left:250px">
                                    <h2>
                                    <?php if (Yii::$app->user->can('eventsEventEditEyeCrewManage')) { ?>

                                    <?php if ($schedule2){ ?>
                                    
                                    <a href="#" class="show-calendars btn pull-left btn-sm" data-scheduleid="<?=$schedule2->id?>" data-roleid="<?=$index?>"><i class="fa fa-arrow-down"></i></a><?php } ?>
                                    <?php $x = \common\models\EventOfferRole::findOne(['event_id'=>$model->id, 'user_role_id'=>$index, 'schedule'=>$key]); if ($x){ ?>
                                    
                                    <?= Html::a('<i class="fa fa-pencil"></i>', ['/crew/update-event-role', 'role_id' => $index, 'event_id'=>$model->id, 'schedule'=>$key], [
                                                            'class' => ' btn pull-left btn-sm add-event-role',
                                                            
                                                        ])
                                                        ?>
                                    <?= Html::a('<i class="fa fa-trash"></i>', ['/crew/delete-event-role', 'role_id' => $index, 'event_id'=>$model->id, 'schedule'=>$key], [
                                                            'class' => ' btn pull-left btn-sm delete-event-role',
                                                            
                                                        ])
                                                        ?>
                                    <?php } ?>
                                    <?php } ?>
                                    <a href="/admin/crew/manage-ajax?id=<?=$model->id?>&role_id=<?=$index?>&schedule=<?=urlencode($key)?>" style="position:relative;" class="assign-user <?=$cl?>" data-role-id="<?=$index?>" data-time_type="1">
                                                        <?= $role['label'] ?> <span class="<?=$class?>"><?= $role['added']."/".$role['quantity']?></span></a>
                                        <div class="team-members pull-right">
                                    <?php foreach ($role['users'] as $user){ 
                                        $team = $user->eventUser->user;
                                        $work = $user->working;
                                            $title = "";
                                            $badge = "";
                                        if ($schedule2){
                                        if (($work->start_time!=$schedule2->start_time)||($work->end_time!=$schedule2->end_time)){
                                                $title = " ".$work->start_time." ".$work->end_time;
                                                $badge = '<span class="badge badge-warning pull-right status-bagde"><i class="fa fa-clock-o"></i></span>';
                                        }}

                                        
                                        $badge2 = "";
                if ((isset($settings['crewConfirm']))&&($settings['crewConfirm']->value==1))
                {
                    if ($user->eventUser->confirm)
                    {
                        $badge2 = '<span class="badge badge-primary pull-right confirm-bagde"><i class="fa fa-check"></i></span>';
                    }else{
                        $badge2 = '<span class="badge badge-default pull-right confirm-bagde"><i class="fa fa-check"></i></span>';
                    }
                }
                                        ?>
                                    <a href="#"   data-userid="<?=$team->id?>"  class="edit_user" style="position:relative;"><?=$badge?><?=$badge2?><img alt="image" class="img-circle img-very-small" src="<?php echo $team->getUserPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name.$title; ?>"></a>
                                    <?php } ?>
                                    <?php 
                                    $missing = $role['quantity']-$role['added'];
                                    for ($i=0; $i<$missing;$i++){ ?>
                                    <a href="/admin/crew/manage-ajax?id=<?=$model->id?>&role_id=<?=$index?>&schedule=<?=urlencode($key)?>" style="position:relative;" class="assign-user <?=$cl?>" data-role-id="<?=$index?>" data-time_type="1"><span class="badge badge-default pull-right status-bagde"><i class="fa fa-plus"></i></span><img alt="image" class="img-circle img-very-small" src="/admin/themes/e4e/images/default-user.png"></a>
                                    <?php } ?>
                                    <a href="/admin/crew/manage-ajax?id=<?=$model->id?>&role_id=<?=$index?>&schedule=<?=urlencode($key)?>" style="position:relative;" class="assign-user <?=$cl?>" data-role-id="<?=$index?>" data-time_type="1"><i class="fa fa-plus"></i></a>
                                    </div>

                                    </h2>
                                    <?php if ($schedule2){ ?>
                                    <div style="display:none" id="calendars_<?=$index?>_<?=$schedule2->id?>">
                                    <?php foreach ($role['users'] as $user){ $team = $user->eventUser->user; $work = $user->working;?>
                                    <p>
                                    <label><?=$team->displayLabel?></label>
                                        <input type="text" class="js-range-slider" data-scheduleid="<?=$schedule2->id?>" data-id="<?=$work->id?>" id="range<?=$work->id?>_<?=$index?>" data-start="<?=substr($work->start_time, 0, 16)?>" data-end="<?=substr($work->end_time, 0, 16)?>" name="range<?=$work->id?>" value="0;10"/>
                                    </p>
                                    <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } ?>

                            </div>
                            <?php } ?>
                           
            </div>
</div>
</div>

<div style="clear:both"></div>
<div class="row" style="margin-top:50px">
        <?php
        $user = Yii::$app->user;
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedUsers(),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap',
                ],
                'options' => [
                    'id' => 'amndjkasrsa',
                ],
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    'last_name',
                    'first_name',
                    'phone',
                    'email',
                    [
                        'label'=>Yii::t('app', 'Rola na evencie'),
                        'value' => function ($user, $key, $index, $column) use ($model)
                        {
                            return \common\models\UserEventRole::getRolesString($user->id, $model->id);
                        },
                    ],
                    [
                        'header' => Yii::t('app', 'Czas pracy'),
                        'content' => function ($user) use ($model) {
                            $workingTime = EventUserPlannedWrokingTime::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $model->id])->orderBy(['start_time'=>SORT_ASC])->all();
                            $toReturn = '';
                            foreach ($workingTime as $work) {
                                $letter = null;
                                if ($model->packing_start == $work->start_time && $model->packing_end == $work->end_time) {
                                    $letter = " (P)";
                                }
                                if ($model->event_start == $work->start_time && $model->event_end == $work->end_time) {
                                    $letter = " (E)";
                                }
                                if ($model->disassembly_start == $work->start_time && $model->disassembly_end == $work->end_time) {
                                    $letter = " (D)";
                                }
                                if ($model->montage_start == $work->start_time && $model->montage_end == $work->end_time) {
                                    $letter = " (M)";
                                }
                                $toReturn .= $work->start_time . " - " . $work->end_time . $letter . "<br>";
                            }
                            return $toReturn;
                        }
                    ],
                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{remove-assignment}',
                        'controllerId'=>'warehouse',
                        'buttons' => [
                            'remove-assignment' => function ($url, $item, $key) use ($model, $user) {
                                $buttons = null;
                                if ($user->can('eventsEventEditEyeCrewDelete')) {
                                    $buttons .= Html::a(Html::icon('remove'), ['/crew/assign-user', 'id'=>$model->id], [
                                        'data'=> [
                                            'itemId'=>$item->id,
                                            'add'=>0,
                                        ],
                                        'class'=>'remove-crew-member'
                                    ]);
                                }
                                if ($user->can('eventsEventEditEyeCrewEdit')) {
                                   $buttons .= Html::icon('pencil', ['style'=>'cursor:pointer;','class'=>'edit_user', 'data' => ['userid' => $item->id ] ]);
                                }
                                return $buttons;
                            }
                        ]
                    ]
                ],
            ])
        ?>
</div>
</div>

<script type="text/javascript">
    var CrewChanged = false;
    var VehicleChanged = false;
    var values = [];
    <?php foreach ($model->eventSchedules as $schedule)
    { if (strlen($schedule->start_time)>15)
        {
            ?>
            values[<?=$schedule->id?>] = [<?php $date = new DateTime($schedule->start_time); while($date->format('Y-m-d H:i')<$schedule->end_time){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($schedule->end_time, 0, 16)."'"; ?> ];
        <?php }
    }
?>
</script>

<?php
/*
foreach (\common\models\EventUserPlannedWrokingTime::find()->where(['event_id'=>$model->id])->all() as $w)
{ 
    foreach (\common\models\EventUserRole::find()->where(['working_hours_id'=>$w->id])->all() as $r)
    {
            $this->registerJs('
            $("#range'.$w->id.'_'.$r->user_event_role_id.'").ionRangeSlider({
                type: "double",
                min:0,
                max: values'.$w->event_schedule_id.'.length,
                from: values'.$w->event_schedule_id.'.indexOf("'.substr($w->start_time, 0, 16).'"),
                to: values'.$w->event_schedule_id.'.indexOf("'.substr($w->end_time, 0, 16).'"),
                values: values'.$w->event_schedule_id.',
                onFinish: function (data) {
                    //zapisujemy
                            var data2 = {
                                working_id: '.$w->id.',
                                start: data.fromValue,
                                end: data.toValue,
                            };
                            $.post("'.Url::to(["crew/update-working-time"]).'", data2, function(response){
                                if (response.success==1)
                                {
                                    toastr.success(response.message);
                                }
                            });
                },
            });
        ');
}
}
*/
$this->registerJs('
$(".add-event-role").click(function(e){
    e.preventDefault();
           var modal = $("#ekipa_modal");
           modal.find(".modalContent").empty();
           modal.modal("show");
            modal.find(".modalContent").load($(this).attr("href"));
             
});

$(".copy-event-role").click(function(e){
    e.preventDefault();
                            var data2 = {
                            };
                            $.post($(this).attr("href"), data2, function(response){
                                $("#tab-crew").empty();
                                 $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
                            });
});

$(".delete-event-role").click(function(e){
    e.preventDefault();
                            var data2 = {
                            };
                            $.post($(this).attr("href"), data2, function(response){
                                $("#tab-crew").empty();
                                 $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
                            });
});

$(".show-calendars").click(function(e){
    e.preventDefault();
    if ($(this).hasClass("active")){

        $("#calendars_"+$(this).data("roleid")+"_"+$(this).data("scheduleid")).hide();
    }else{
        $("#calendars_"+$(this).data("roleid")+"_"+$(this).data("scheduleid")).show();
        $("#calendars_"+$(this).data("roleid")+"_"+$(this).data("scheduleid")).find(".js-range-slider").each(function()
        {
            $(this).ionRangeSlider({
                type: "double",
                min:0,
                max: values[$(this).data("scheduleid")].length,
                from: values[$(this).data("scheduleid")].indexOf($(this).data("start")),
                to: values[$(this).data("scheduleid")].indexOf($(this).data("end")),
                values: values[$(this).data("scheduleid")],
                onFinish: function (data) {
                    //zapisujemy
                            var data2 = {
                                working_id: data.input.data("id"),
                                start: data.fromValue,
                                end: data.toValue,
                            };
                            $.post("'.Url::to(["crew/update-working-time"]).'", data2, function(response){
                                if (response.success==1)
                                {
                                    toastr.success(response.message);
                                }
                            });
                },
            });
        });
    }
    $(this).toggleClass("active");
    
});

$(".assign-user").click(function(e){
    e.preventDefault();
    if ($(this).hasClass("no-time"))
    {
        alert ("Nim przypiszesz pracowników do tego etapu, należy uzupełnić jego harmonogram.");
    }else{
           var modal = $("#ekipa_modal");
            modal.find(".modalContent").empty().load($(this).attr("href"));
            modal.modal("show"); 
    }

});

$("#ekipa_modal").on("hidden.bs.modal", function () {
    // do something…
    //przeładowujemy okienko ekipy
    if (CrewChanged)
    {
        $("#tab-crew").empty();
        $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
    }
    if (VehicleChanged)
    {
        $("#tab-vehicle").empty();
        $("#tab-vehicle").load("'.Url::to(["event/vehicle-tab", 'id'=>$model->id]).'");
    }
});

$("#selectWorkingTime").change(function(){
    $(".wt-all").hide();
    if ($(this).val()==1)
    {
        $(".wt-all").show();
    }
    if ($(this).val()==2)
    {
        $(".packing").show();
    }
    if ($(this).val()==3)
    {
        $(".montage").show();
    }
    if ($(this).val()==4)
    {
        $(".event").show();
    }
    if ($(this).val()==5)
    {
        $(".disassembly").show();
    }
});

$("body").on("click", ".edit_user", function(e){
    e.preventDefault();
    openUserDetailsModal('.$model->id.', $(this).data("userid"));

});

function openUserDetailsModal(event_id, user_id){
    var modal = $("#ekipa_modal");
    modal.modal("show");
    modal.find(".modalContent").empty();
    modal.find(".modalContent").load("'.Url::to(["planboard/user-form"]).'?event_id="+event_id+"&user_id="+user_id+"&role=0&in_event=1");
    
}

$(".remove-crew-member").click(function(e){
    e.preventDefault();
    swal({
            title: "'.Yii::t('app', 'Na pewno chcesz usunąć?').'",
            text: "'.Yii::t('app', 'Odpięcie pracownika spowoduje usunięcie jego godzin pracy dla tego wydarzenia').'",
            icon:"info",
          buttons: {
            cancel: "'.Yii::t('app', 'Nie').'",
            yes: {
              text: "'.Yii::t('app', 'Tak').'",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              $.post($(this).prop("href"), $(this).data());
                $(this).parent().parent().remove();
                $("#tab-crew").empty();
                $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
              break;       
          }
        });
    
});

$(".send-noti").click(function(e){
    e.preventDefault();
    var el = $(this);
    $.post($(this).prop("href"), null, function(){
        alert("Powiadomienia zostały wysłane");
        el.hide("slow");
    });
}); 

$("#sendreminderscheckbox").change(function(){
    $.post("'.Url::to(["event/save-reminders", 'id'=>$model->id]).'", {send_reminders:$(this).prop("checked")});
});

$(".send-reminders").click(function(e){
    e.preventDefault();
    $.post($(this).prop("href"), null, function(){
        toastr.success("'.Yii::t('app', 'Powiadomienia zostały wysłane.').'");
    });
});

$(".notification-checkbox").change(function(){
    var noti = 0;
    if ($(this).prop("checked")) {
        noti = 1;
    }
    if (!$(this).prop("checked")) {
        $(".send-noti").slideDown("slow");
    }
    else {
        $(".send-noti").slideUp("slow");
    }

    var data = { main: { eventNotifications: noti } };
    $.post("'.Url::toRoute(['setting/change-event-notifications']).'", data);
});

$(".copy-modal-crew").on("click", function(e){
    e.preventDefault();
    var modal = $("#copy_modal_crew");
    modal.modal("show").find(".modalContent").load($(this).attr("href"));
});



');

$this->registerJs('
    $(".add-schedule2").click(function(e){
        e.preventDefault();
        $("#schedule-modal").find(".modal-body").empty();
        $("#schedule-modal").modal("show").find(".modal-body").load($(this).attr("href"));
    });
    $(".add-schedule2").on("contextmenu",function(){
       return false;
    }); 
');

$this->registerCss('
@media (min-width: 1200px){
.modal-lg {
    width: 1200px;
}
}
    ');