<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
\common\assets\Gmap3Asset::register($this);
use yii\bootstrap\Modal;
use yii\helpers\Url;

use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $addon \common\models\EventUserAddon */
/* @var $workingTime \common\models\EventUserWorkingTime */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
$formatter = Yii::$app->formatter;

Modal::begin([
    'header' => Yii::t('app', 'Ekipa'),
    'id' => 'ekipa_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

Modal::begin([
    'header' => Yii::t('app', 'Flota'),
    'id' => 'vehicle_modal',
        'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Kopiuj ekipę z innego wydarzenia')."</h4>",
    'id' => 'copy_modal_crew',
    'options'=>[
    'tabindex' => false,],
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
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
    var sort = "cat";
    $(".add-task").click(function(e){
        $("#new-task").find(".modalContent").empty();
        e.preventDefault();
        $("#new-task").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');
?>
<div class="event-view">
        <p>
        <?php if (Yii::$app->user->can('chatCreate')) { ?>
        <?= Html::a('<i class="fa fa-envelope"></i> ' . Yii::t('app', 'Rozmowa grupowa'), ['#'], ['class' => 'btn btn-success', 'onclick'=>'createEventChat('.$model->id.'); return false;']) ?>
        <?php } ?>
        <?php if (Yii::$app->user->can('eventEventEditPencil')) { ?>
            <?php if ((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent'))) { ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
        }  } ?>
        <?php if (Yii::$app->user->can('eventEventDelete')) { ?>
            <?php if ((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent'))) { ?>
            <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete',
                'id' => $model->id], ['class' => 'btn btn-danger',
                'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć? Kasujesz wszystkie rezerwacje, a także wydania i przyjęcia w tym wydarzeniu.'), 'method' => 'post',],]);
        } } ?>
        <?php if (Yii::$app->user->can('eventEventEditPencil')) { 
            if (isset($model->eventStatut))
                $statuts = \common\models\EventStatut::find()->where(['type'=>1, 'active'=>1, 'button'=>1])->andWhere(['>', 'position', $model->eventStatut->position])->all();
            else
                $statuts = \common\models\EventStatut::find()->where(['type'=>1, 'active'=>1, 'button'=>1])->all();
            foreach ($statuts as $s)
            {
                $title = $s->name;
                if ($s->icon)
                {
                    $title = '<i class="fa '.$s->icon.'"></i> '.$title;
                }
                $data_confirm = 0;
                if (($s->delete_gear)||($s->delete_crew))
                    $data_confirm = 1;
                echo " ".Html::a($title, ['#'], ['class' => 'btn status-button', 'data-id'=>$s->id, 'style'=>'color:white; background-color:'.$s->color, 'data-conf' => $data_confirm]);
            }

     }

     if ($user->can('eventsEventAdd')) {
                        echo " ".Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Duplikuj'), ['create', 'event_id'=>$model->id], ['class' => 'btn btn-success']);
                    }
      ?>
    </p>
<div class="row">
    <div class="col-md-4">
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                            <div class="ibox-tools">
                            <span class="label label-warning-light pull-right"> <?= Yii::t('app', 'ID') ?>: <?php echo $model->code; ?></span>
                            </div>
                        </div>
                        <div class="ibox-content">
                        <?php 
                            $teams = $model->getAssignedUsers();

                            if ($teams->getTotalCount()>0){
                                $total = 0;
                        ?>
                            <div class="team-members">
                            <?php  foreach ($teams->getModels() as $team){ 
                                $total++;
                                if ($total<20){?>
                            <a href="#"  title="<?=$team->first_name." ".$team->last_name; ?>"><?=$team->getUserPhoto("img-circle img-small")?></a>
                            <?php } }?>
                            </div>
                        <?php } ?>
                        <?php if (Yii::$app->user->can('eventEventEditEyeClientDetails')) { ?>
                        
                                <h4 class="font-bold"> <?= Yii::t('app', 'Klient') ?>: <?php echo Html::a($model->customer->name." (NIP:".$model->customer->nip.")", ['/customer/view', 'id'=>$model->customer_id]); ?></h4>
                                <?php if (isset($model->contact)){ ?>
                                <div><?php echo $model->contact->first_name." ".$model->contact->last_name;?>
                                <?php if ($model->contact->phone!="") { ?>
                                 <span class="label label-primary"><i class="fa fa-phone"></i> <?php echo $model->contact->phone; ?></span>                           
                                <?php } ?>
                                <?php if ($model->contact->email!="") { ?>
                                 <span class="label label-warning"><i class="fa fa-envelope"></i> <?php echo $model->contact->email; ?></span>                           
                                <?php } ?>                               
                                </div>
                                <?php } ?>
                        <?php } ?>
                                <h4 class="font-bold" id="event-termin"> <?= Yii::t('app', 'Termin') ?>: <?php echo Yii::$app->formatter->asDateTime($model->event_start,'short')." - ".Yii::$app->formatter->asDateTime($model->event_end, 'short'); ?></h4>
                        <?php 
                            $departments = $model->getDepartmentList();

                            if ($departments){
                        ?>
                            <h4><?= Yii::t('app', 'Działy') ?></h4>
                            <p class="departments">
                                <?php foreach ($departments as $department){ ?>
                                <span class="label label-primary"><?=$department?></span>
                                <?php } ?>
                            </p>
                            <?php } ?>
                            <?php  if ($model->info!="") { ?>
                            <h4><?= Yii::t('app', 'Uwagi') ?></h4>
                            <p><?php echo $model->info; ?></p>
                            <?php } ?>
                            <h4><?= Yii::t('app', 'Dojazd') ?></h4>
                            <p><?= Yii::t('app', 'MAGAZYN') ?>: <?php echo $model->getOriginAddress(); ?><br/><?= Yii::t('app', 'MIEJSCE') ?>: <?php echo $model->getDestinationAddress(); ?></p>
                            <p><?= Yii::t('app', 'STATUS') ?>:
                            <?php
                              if ((Yii::$app->user->can('eventsEventEditStatus'))&&((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent')))){ 
                                echo Html::dropDownList('status', $model->status, \common\models\Event::getStatusList($model->status), ['id'=>'eventStatus']);
                                }else{
                                    echo $model->getStatusButton();
                                }
                            
                                echo " ".Html::a("<i class='fa fa-list'></i>", ['#'], ['class' => 'btn btn-warning btn-xs status-history', 'onclick'=>' if ($(this).hasClass("active")){$(".status-history-list").hide();}else{$(".status-history-list").show();} $(this).toggleClass("active"); return false;']);
                                ?>
                            </p>
                            <?php
                            if (Yii::$app->user->can('eventsEventEditStatus')) {
                            foreach (\common\models\EventAdditionalStatut::find()->where(['active'=>1])->all() as $s)
                            { if ($s->showToUser())
                            {?>
                                <p><?= $s->name ?>: 
                                <?php echo Html::dropDownList('status'.$s->id, $model->getAdditionalStatut($s->id), $s->getStatusList(), ['class'=>'eventStatus', 'data-id'=>$s->id]); ?>
                                </p>
                            <?php } } }
                            ?>
                                                        <?php
                            if (\Yii::$app->params['companyID']=="newsystem"){
                                if (Yii::$app->user->can('eventEventEditPencil')) {
                                    ?>
                                <p><?= Yii::t('app', 'POZIOM SCENOGRAIA') ?>: <?php echo Html::dropDownList('scenography_level', $model->scenography_level, [0,1,2,3,4,5], ['id'=>'eventScLevel']); ?></p>

                                <?php }else{ ?>
                                <p><?= Yii::t('app', 'POZIOM SCENOGRAIA') ?>: <?=$model->scenography_level?></p>
                                <?php }
                            }
                            ?>
                            <div class="status-history-list" style="display:none">
                            <strong><?=Yii::t('app', 'Historia zmian statusów')?></strong>
                            <div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->getStatusHistory() as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">
                                            <strong><?=$m->user->displayLabel?>: </strong><?=$m->text?></br>
                                            <small class="text-navy"><?=$m->datetime?></small>
                                        </div>
                                        </div>
                                        <?php
                                        }   
                                    ?>                                
                                </div>
                            </div>
                            </div>
                                <div class="row" style="margin-top:20px;">
                                    <?php $status = $model->getTaskStatus(); ?>
                                <div class="col-lg-12">
                                            <div class="progress progress-striped active m-b-sm">
                                                <div style="width: <?=$status['status']?>%;" class="progress-bar"></div>
                                            </div>
                                            
                                            <small><strong><?=Yii::t('app', 'Status')?></strong> - <?=Yii::t('app', 'wykonano')?> <strong><?=$status['status']?>%</strong>. <?=Yii::t('app', 'Jest to')?> <?=$status['done']?>/<?=$status['task']?> zadań.</small>
                                </div>
                            </div>
                            <div class="row  m-t-sm">
                                <div class="col-sm-4">
                                    <div class="font-bold"><?= Yii::t('app', 'POZIOM') ?></div>
                                    <?php echo $model->level; ?>
                                </div>
                                <div class="col-sm-8 text-right">
                                    <div class="font-bold"><?= Yii::t('app', 'DODAŁ') ?></div>
                                    <?php echo $model->creator->first_name." ".$model->creator->last_name; ?>
                                </div>
                            </div>

                        </div>
                    </div>
            </div>    
    </div>
 
    </div>

    <div class="col-md-3">
        <?php if (isset($model->manager)) { ?>
        <div class="row">
        <div class="col-lg-12">
                    <div class="profile-image">
                    <?php  if ($model->manager->getPhotoUrl()!="") { ?>
                        <img src="<?=$model->manager->getPhotoUrl()?>" class="img-circle circle-border m-b-md" alt="profile">
                        <?php } ?>
                    </div>
                    <div class="profile-info">
                        <div class="">
                            <div>
                                <h2 class="no-margins">
                                    <?php echo $model->manager->first_name." ".$model->manager->last_name;?>
                                </h2>
                                <h4><?=Yii::t('app', 'Project Manager')?></h4>
                                <small>
                                <?php if ($model->manager->email!="") { ?>
                                                            <p style="margin:0">
                                                                <span class="fa fa-envelope m-r-xs"></span>
                                                                <?php echo $model->manager->email; ?>
                                                            </p>
                                                            <?php } ?>
                                                            <?php if ($model->manager->phone!="") { ?>
                                                            <p>
                                                                <span class="fa fa-phone m-r-xs"></span>
                                                                <?php echo $model->manager->phone; ?>
                                                            </p>
                                                            <?php } ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <?php } ?>

        <?php  if (isset($model->location)) { ?>
    <?php if (Yii::$app->user->can('eventEventEditEyeClientDetails')) { ?>
    <div class="row">
    <div class="col-md-12 no-padding" style="background:white; margin-bottom:20px;">
                        <div class="col-md-6 no-margins no-padding">
                            <div class="ibox-content no-padding border-left-right">
                            <?php if ($model->location->getPhotoUrl()){ ?>
                                <img alt="image" class="img-responsive" src="<?php echo $model->location->getPhotoUrl(); ?>">
                            <?php } ?>
                            </div>
                            <div class="ibox-content profile-content">
                                <h4><strong><?= Html::a($model->location->name , ['/location/view', 'id' => $model->location->id], ['target' => '_blank']) ?></strong></h4>
                                <?php if ($model->location->address!="") { ?>
                                <p><i class="fa fa-map-marker"></i> <?php echo $model->location->address.", ".$model->location->zip." ".$model->location->city ?></p>
                                <?php } ?>
                                <?php if ($model->location->manager_phone!="") { ?>
                                    <p><i class="fa fa-phone"></i> <?php echo $model->location->manager_phone; ?></p>
                                <?php } ?>
                                <?php if ($model->location->electrician_phone!="") { ?>
                                <p><i class="fa fa-plug"></i> <?php echo $model->location->electrician_phone ?></p>
                                <?php } ?>
                                <?php if ($model->location->address!="") { ?>
                                <p><i class="fa fa-truck"></i> <?php echo $model->location->getGoogleDistance()." ".Yii::t('app', 'km')." "; ?>
                                <?php if ($model->location->address!="") { ?>
                                <a href="http://maps.google.com/?q=<?php echo $model->location->address.", ".$model->location->zip." ".$model->location->city ?>" class="btn btn-primary btn-xs" type="button"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;<?= Yii::t('app', 'Prowadź') ?></a>
                                <?php } ?>
                                </p>
                                <?php } ?>
                                <?php if ($model->location->info!="") { ?>
                                <h5>
                                    <?= Yii::t('app', 'Informacje') ?>
                                </h5>
                                <p>
                                    <?php echo $model->location->info; ?>
                                </p>
                                <?php } ?>

                    </div>

                </div>                    
                <div class="col-md-6 no-margins no-padding" style="max-height:300px; overflow-y:scroll; overflow-x:scroll">
                <div class="ibox-content border-left-right" style="padding:2px">
                <h4><?=Yii::t('app', 'Plany techniczne')?></h4>
                <table class="table narrow-padding">
                    <?php $i=0; foreach ($model->location->locationPlans as $plan){ $i++;
                        echo "<tr><td>".$i.".</td><td>".Html::a($plan->getName(), $plan->getFileUrl())."</td><td>".Html::a("<i class='fa fa-save'></i>", ['location-plan/download', 'id'=>$model->id])."</td></tr>";
                        }?>          
                        </table> 
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['location-plan/create', 'locationId'=>$model->location_id], ['class' => 'btn btn-success btn-xs']) ?>                     

                    </div>
                <div class="ibox-content border-left-right" style="padding:2px">
                                <h4><?=Yii::t('app', 'Panoramy')?></h4>
                    <table class="table narrow-padding">
                    <?php $i=0; foreach ($model->location->locationPanoramas as $panorama){ $i++;
                        echo "<tr><td>".$i.".</td><td>".Html::a($panorama->getName(), ['location-panorama/show', 'id'=>$panorama->id])."</td></tr>";
                        }?>
                        </table>
                        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['location-panorama/create', 'locationId'=>$model->location_id], ['class' => 'btn btn-success btn-xs']) ?>
                    </div>
                    </div>

    </div>
    </div>
        <?php } ?>
      <?php } ?>

        <div class="row">
        <div class="col-md-12">
        <?php if (\Yii::$app->params['companyID']!="redbull"){ ?>
        <div class="row">
                    <?php
            $data = $model->getGearsSummary();
            ?>
            <div class="col-lg-3" style="padding:0px 2px">
        <span class="label label-primary"  style="font-size:11px"><i class="fa fa-plug"></i> <?php echo $formatter->asDecimal($data['weight'],0); ?> <?= Yii::t('app', 'kg') ?></span>
                </div>
                <div class="col-lg-3" style="padding:0px 2px">
                <span class="label label-danger"  style="font-size:11px"><i class="fa fa-archive"></i> <?php echo $formatter->asDecimal($data['volume'],1); ?> <?= Yii::t('app', 'm') ?></span>
                </div>
                <div class="col-lg-3" style="padding:0px 2px">
        <span class="label label-warning"  style="font-size:11px"><i class="fa fa-truck"></i> <?php echo $formatter->asDecimal($data['vehicle_volume'],1); ?> <?= Yii::t('app', 'm') ?></span>
                </div>
                        <div class="col-lg-3" style="padding:0px 2px">
        <span class="label label-success"  style="font-size:11px"><i class="fa fa-bolt"></i> <?php echo $formatter->asDecimal($data['power_consumption'],0); ?> <?= Yii::t('app', 'W') ?></span>
                </div>
                        
        </div>
        <?php } ?>
            </div>
        </div>

    </div>

    <div class="col-md-5">
        <?php if (Yii::$app->user->can('eventEventEditPencil')) { ?>
                        <div class="ibox">
                        <div class="ibox-title">                        <h3>
                        <?php echo Yii::t('app', 'Harmonogram'); ?>
                        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj etap'), ['add-schedule', 'id' => $model->id], [
                                                            'class' => 'btn btn-xs  btn-success add-schedule ',
                                                            
                                                        ])
                                                        ?>
                        </h3> </div>
                        <div class="ibox-content no-padding" style="border:0">   
                            <?php if ($model->packing_type)
                        { ?>
                        <div class="alert alert-danger">
                                Zmieniłeś harmonogram wydarzenia, aby zmienić rezerwację sprzętu zmień czasy rezerwacji w odpowiednich grupach sprzętowych w zakładece sprzęt. <?=Html::a(Yii::t('app', "Ukryj komunikat"), ['/event/set-schedule-change-ok', 'id'=> $model->id], ['class'=>'btn btn-danger btn-xs', 'id'=>'schedulebutton'])?>
                            </div>
                        <?php } ?>
                                <ul class="todo-list ui-sortable" id="schedule-list">
                                <?php foreach ($model->eventSchedules as $schedule){
                                    $color = "";
                                    if ($schedule->color)
                                        $color = " border-color:".$schedule->color; ?>
                                <li class="checklist-item" draggable="true" id="bigitem-<?=$schedule->id?>" style="padding:1px; background-color:white;<?=$color?> ">
                                <div class="row">
                                <div class="col-xs-4" style="padding-left:20px; padding-top:0px;">
                                <span><?=$schedule->name?></span>
                                </div>
                                <div class="col-xs-6 no-padding">
                                                                <?php
                                $form = ActiveForm::begin([
                                    'enableAjaxValidation' => false,
                                    'enableClientScript' => false,
                                    'options'=>['class'=>'form-vertical']
                                ]);
                                echo $form->field($schedule, 'id')->hiddenInput()->label(false);
                                    echo $form->field($schedule, 'dateRange')->widget(\common\widgets\DateRangeField::className(), ['options'=>[ 'id'=>'s'.$schedule->id, 'class' => 'form-control schedule-date-range', 'autocomplete'=>"off"]])->label(false);

                                ActiveForm::end();
                                    ?>
                                </div>
                                <div class="col-xs-2 no-padding">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', ['update-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-xs  add-schedule',
                                                            
                                                        ])
                                                        ?>
                                                        <?= Html::a('<i class="fa fa-trash"></i>', ['delete-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-danger btn-xs',
                                                            'data' => [
                                                                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                                                'method' => 'post',
                                                            ],
                                                        ])
                                                        ?>

                                

                                </div>
                                </div>
                                </li>
                                <?php } ?>
                                </ul>
                        </div>
                        </div> 
            <?php }else{ 
 ?>
 <div class="ibox">
 <div class="ibox-title">
<h5><?=Yii::t('app', 'Harmonogram')?></h5>
 </div>
 <div class="ibox-content">
 <table class="table table-hover issue-tracker">
                                <tbody>
                <?php foreach ($model->eventSchedules as $schedule)
                { ?>
                                    <tr>
                                    <td>
                                        <h4><i class="fa fa-clock-o"></i> <?=$schedule->name?></h4>
                                    </td>
                                    <td>
                                    <?=substr($schedule->start_time,0,10)?><br><small><?=substr($schedule->start_time,11,5)?></small>
                                    </td>
                                    <td>
                                    <?=substr($schedule->end_time,0,10)?><br><small><?=substr($schedule->end_time,11,5)?></small>
                                    </td>
                                    </tr>
            <?php    } ?>
</tbody>
</table>
</div>
</div>

        <?php     } ?>

    </div>
</div>

</div>
        <div class="tabs-container">
            <?php 
            //!!!: Zmiana zakładek -> zmiana indexu w js (google maps)
            

                if (Yii::$app->session->get('company')!=1)
                {
                $tabItems = [
                
                [
                    'label'=>Yii::t('app', 'Zadania'),
                    'content'=>$this->render('_tabTask', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-task',
                    ],
                    'active'=>true,
                ],
                [
                    'label'=>Yii::t('app', 'Szczegóły'),
                    'content'=>$this->render('_tabCalendar', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeCalendar'),
                    'options'=> [
                        'id'=>'tab-calendar',
                    ],
                ],
                
                [
                    'label'=>Yii::t('app', 'Sprzęt'),
                    'content'=>$this->render('_tabGear', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeGear'),
                    'options'=> [
                        'id'=>'tab-gear',
                    ]
                ],
                ];
                    $tabItems[] =
                                    [
                    'label'=>$model->getAssignedOuterGearModelsNumber().Yii::t('app', 'Usługi'),
                    'content'=>$this->render('_tabOuterGear', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeOuterGear'),
                    'id'=>'tab-outer-gear',
                    'options'=> [
                        'id'=>'tab-outer-gear',
                    ],

                
                    'items'=>[
                    
                    [
                        'label'=>Yii::t('app', 'Zapotrzebowanie na usługi').$model->getAssignedOuterGearModelsNumber(),
                        'content'=>$this->render('_tabOuterGear2', ['model'=>$model]),
                        'encode'=>false,
                        'options'=> [
                            'id'=>'tab-outer1',
                        ]
                    ],
                    [
                        'label'=>Yii::t('app', 'Usługi zarezerwowane'),
                        'content'=>$this->render('_tabOuterGear1', ['model'=>$model]),
                        'options'=> [
                            'id'=>'tab-outer3',
                        ]
                    ],
                    
                    [
                        'label'=>Yii::t('app', 'Konflikty').$model->getConflictCount(),
                        'content'=>$this->render('_tabOuterGear3', ['model'=>$model]),
                        'encode'=>false,
                        'options'=> [
                            'id'=>'tab-outer2',
                        ]
                    ]


        
                ]
                ];
                }else{
                                $tabItems = [
                                
                [
                    'label'=>Yii::t('app', 'Zadania'),
                    'content'=>$this->render('_tabTask', ['model'=>$model]),
                    'visible'=>true,
                    'options'=> [
                        'id'=>'tab-task',
                    ],
                    'active'=>true,
                ],
                [
                    'label'=>Yii::t('app', 'Szczegóły'),
                    'content'=>$this->render('_tabCalendar', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeCalendar'),
                    'options'=> [
                        'id'=>'tab-calendar',
                    ],
                ], 
                
                [
                    'label'=>Yii::t('app', 'Sprzęt'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeGear'),
                    'options'=> [
                        'id'=>'tab-gear',
                    ]
                ], 
                ];
                
                    $tabItems[] =
                                    [
                    'label'=>$model->getConflictCount()." ".$model->getAssignedOuterGearModelsNumber().Yii::t('app', 'Sprzęt zewnętrzny'),
                    'content'=>"",
                    'visible'=>$user->can('eventEventEditEyeOuterGear'),
                    'id'=>'tab-outer-gear',
                    'options'=> [
                        'id'=>'tab-outer-gear',
                    ],

                
                    'items'=>[

                    
                    [
                        'label'=>Yii::t('app', 'Zapotrzebowanie na sprzęt zewnętrzny').$model->getAssignedOuterGearModelsNumber(),
                        'content'=>$this->render('_tabOuterGear2', ['model'=>$model]),
                        'encode'=>false,
                        'options'=> [
                            'id'=>'tab-outer1',
                        ]
                    ],
                    
                    [
                        'label'=>Yii::t('app', 'Sprzęt zarezerwowany u wypożyczającego'),
                        'content'=>$this->render('_tabOuterGear1', ['model'=>$model]),
                        'options'=> [
                            'id'=>'tab-outer3',
                        ]
                    ],
                    
                    [
                        'label'=>Yii::t('app', 'Konflikty').$model->getConflictCount(),
                        'content'=>$this->render('_tabOuterGear3', ['model'=>$model]),
                        'encode'=>false,
                        'options'=> [
                            'id'=>'tab-outer2',
                        ]
                    ]
                    
        
                ]
                ];
                
                }
                
                $tabItems[] = 
                
                [
                    'label'=>Yii::t('app', 'Załączniki').' <span class="badge badge-primary pull-right">'.count($model->attachments).'</span>',
                    'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeAttachment'),
                    'options'=> [
                        'id'=>'tab-attachment',
                    ]
                ];
if (Yii::$app->session->get('company')!=1)
{
    $tabItems[] =[
                    'label'=>Yii::t('app', 'Kosztorysy'),
                    'content'=>$this->render('_tabEstimate', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEstimate'),
                    'options'=> [
                        'id'=>'tab-estimate',
                    ]
                ];
    $tabItems[] =            [
                    'label'=>Yii::t('app', 'Umowy'),
                    'content'=>$this->render('_tabDeal', ['model'=>$model]),
                    'visible'=>$user->can('eventEventDeal'),
                    'options'=> [
                        'id'=>'tab-deal',
                    ]
                ];
    $tabItems[] =            [
                    'label'=>Yii::t('app', 'Briefy'),
                    'content'=>$this->render('_tabBrief', ['model'=>$model]),
                    'visible'=>$user->can('eventEventBrief'),
                    'options'=> [
                        'id'=>'tab-brief',
                    ]
                ];
    $tabItems[] =            [
                    'label'=>Yii::t('app', 'Oferty AE'),
                    'content'=>$this->render('_tabAgencyOffers', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeOffer'),
                    'options'=> [
                        'id'=>'tab-agency-offer',
                    ]
                ];
}else{
     $tabItems[] =           [
                    'label'=>Yii::t('app', 'Oferty'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeOffer'),
                    'options'=> [
                        'id'=>'tab-offer',
                    ]
                ];
}

 $tabItems2 =   [ 
 
                [
                    'label'=>Yii::t('app', 'Ekipa'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeCrew'),
                    'options'=> [
                        'id'=>'tab-crew',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Flota'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeVehicles'),
                    'options'=> [
                        'id'=>'tab-vehicle',
                    ]
                ], 
                [
                    'label'=>Yii::t('app', 'Powiadomienia'),
                    'content'=>$this->render('_tabMessage', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeNotifications'),
                    'options'=> [
                        'id'=>'tab-message',
                    ]
                ],
                
                [
                    'label'=>Yii::t('app', 'Godziny pracy'),
                    'content'=>$this->render('_tabWorkingTime', ['model'=>$model,'workingTime'=>$workingTime]),
                    'visible'=>$user->can('eventsEventEditEyeWorkingHours'),
                    'options'=> [
                        'id'=>'tab-working-time',
                    ]
                ], 
                
                [
                   'label'=>Yii::t('app', 'Finanse'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeFinance'),
                    'options'=> [
                        'id'=>'tab-finances',
                    ]
                ], 
                [
                   'label'=>Yii::t('app', 'Statystyki'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventStat'),
                    'options'=> [
                        'id'=>'tab-statistic',
                    ]
                ],
                [
                   'label'=>Yii::t('app', 'Aktualności'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventNews'),
                    'options'=> [
                        'id'=>'tab-notes',
                    ]
                ],
                [
                   'label'=>Yii::t('app', 'Historia'),
                    'content'=>$this->render('_tabGearEmpty', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeHistory'),
                    'options'=> [
                        'id'=>'tab-log',
                    ]
                ],
    
            ];
            $tabItems = array_merge($tabItems, $tabItems2);
            
            echo TabsX::widget([
                'items'=>$tabItems,
                'id'=>'eventTabs',
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,
                'pluginEvents'=> [
                    'shown.bs.tab'=>'function(e){
                        var id = $(this).find("ul").prop("id");
                        var tab = $(this).find("ul li.active");                                                
                        var index = $(this).find("ul li").index(tab);
                        
                        var vehicleTabIndex = $("#tab-vehicle").index(".tab-pane")+1;
                        var statTabIndex = $("#tab-statistic").index(".tab-pane")+1;
                        var crewIndex = $("#tab-crew").index(".tab-pane");

                        if (index==crewIndex)
                        {
                            //$("#tab-crew").empty();
                            //$("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
                        }
                        if (index==statTabIndex)
                        {
                            //loadEventChart();
                        }
                        if (index == vehicleTabIndex)
                        {
                        $("#map")
                           .gmap3({
                            center: [52.140992, 19.215291],
                            zoom: 6,
                            mapTypeId : google.maps.MapTypeId.ROADMAP,
                            scrollwheel: false,
                          })
                          .route({
                            origin:"'.$model->getOriginAddress().'",
                            destination:"'.str_replace('"', '',$model->getDestinationAddress()).'",
                            travelMode: google.maps.DirectionsTravelMode.DRIVING,
                            provideRouteAlternatives: true,
                          })
                          .directionsrenderer(function (results) {
//                                    for (var i = 0, len = results.routes.length; i < len; i++) {
//                                        new google.maps.DirectionsRenderer({
//                                            map: window.map,
//                                            directions: results,
//                                            routeIndex: i
//                                        });
//                                    }
                            if (results) {
                              return {
                                panel: "#route",
                                directions: results,
                                draggable : true,
                              }
                            }
                          })
//                          .transitlayer()
//                          .trafficlayer()
                          .fit();
                        }
                    }'
                ]
            ]);

            ?>

        </div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
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
        var newRow = '<li class="warning-element ui-sortable-handle" id="task'+response.id+'" style="'+style+'"><a href="#" class="check-link" data-id="'+response.id+'"><i class="fa fa-square-o"></i> </a><strong>'+response.title+'</strong><div class="agile-detail">';
        for(var i=0; i<response.users.length; i++)
            newRow+='<a href="#" class="pull-right btn btn-xs btn-warning" style="'+style2+'">'+response.users[i]+'</a>'
        newRow +='<i class="fa fa-clock-o"></i>'+response.end_time+'</div></li>';
        $("#todo").append(newRow);
    }
    function makeDone(el)
    {
                var element = el;
                $.ajax({
                  url: '<?=Url::to(['task/done'])?>?id='+element.data( 'id' ),
                  success: function(response){
                    if (response.status==10)
                    {
                        element.empty().append('<i class="fa fa-check-square"></i>');
                    }else{
                        element.empty().append('<i class="fa fa-square-o"></i>');
                    }
                  }
                });        
    }

    function openTask(id)
    {
        url = '<?=Url::to(['task/view'])?>?id='+id;
        var win = window.open(url, '_blank');
        win.focus();
    }

    function loadTabs()
    {
        <?php if ($user->can('eventEventEditEyeGear'))
        { ?>
            $("#tab-gear").empty().load("<?=Url::to(['event/gear-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeOffer'))
        { ?>
            $("#tab-offer").empty().load("<?=Url::to(['event/offer-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeCrew'))
        { ?>
            $("#tab-crew").empty().load("<?=Url::to(['event/crew-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeVehicles'))
        { ?>
            $("#tab-vehicle").empty().load("<?=Url::to(['event/vehicle-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeFinance'))
        { ?>
            $("#tab-finances").empty().load("<?=Url::to(['event/finance-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventStat'))
        { ?>
            $("#tab-statistic").empty().load("<?=Url::to(['event/stat-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventNews'))
        { ?>
            $("#tab-notes").empty().load("<?=Url::to(['event/notes-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeHistory'))
        { ?>
            $("#tab-log").empty().load("<?=Url::to(['event/log-tab', 'id'=>$model->id])?>");
        <?php } ?>
              
    }
</script>

<?php
 $this->registerJs("
    
    loadTabs();
            $('.task-link').click(function () {
                
                makeDone($(this));
                return false;
        });
            $('.all-tasks').click(function (e) {
                e.preventDefault();
                $('#todo').find('li').show();
                return false;
        });
            $('.my-tasks').click(function (e) {
                e.preventDefault();
                $('#todo').find('li.all').hide();
                return false;
        });
        ");

 $this->registerCss("
    .table.narrow-padding td{
        padding-top:3px;
        padding-bottom:3px;

    }
    .table.narrow-padding{
        margin-bottom:5px;
        max-height:200px;
        overflow-y:scroll;
        overflow-x:scroll;
    }
    ");
?>

<script type="text/javascript">
    var eventStatuts= [];
    <?php
    $statuts = \common\models\EventStatut::find()->asArray()->all();
    foreach ($statuts as $s)
    {
        if (($s['delete_gear'])||($s['delete_crew']))
        {
            echo "eventStatuts[".$s['id']."]=1;";
        }else{
            echo "eventStatuts[".$s['id']."]=0;";
        }
    }
    ?>
    function changeEventStatus(status)
    {
        if (eventStatuts[status]==1)
        {
            swal({
            title: "<?=Yii::t('app', 'Uwaga!')?>",
            icon:"warning",
            text: "<?=Yii::t('app', ' Zmiana na ten status może usunąć rezerwacje pracowników i sprzętu z tego eventu. Czy chcesz kontynuować?')?>",
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
              $.ajax({
            url: '<?=Url::to(['event/change-status'])?>?event_id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                        location.reload();
                                  }
            });
              break;       
          }
        });      
        }else{
            $.ajax({
            url: '<?=Url::to(['event/change-status'])?>?event_id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                        location.reload();
                                  }
            });
        }
        
    }


    function changeEventAdditionalStatus(status, id)
    {
            $.ajax({
            url: '<?=Url::to(['event/change-additional-status'])?>?event_id=<?=$model->id?>'+'&status='+status+'&id='+id,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                        //location.reload();
                                  }
            });
    }
    function changeScLevel(status)
    {
        $.ajax({
            url: '<?=Url::to(['event/change-sc-level'])?>?event_id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Poziom zmieniony')?>');
                                  }
            });
    }
</script>
<?php
$this->registerJs('

    $("#schedulebutton").on("click", function(e){
e.preventDefault();
var t = $(this);
$.ajax({
            data: [],
            type: "POST",
            url: t.attr("href"),

        }).done(function(success) {
              t.parent().hide();
            });
});

$("#eventStatus").on("change", function(){
    changeEventStatus($(this).val());
});

$(".eventStatus").on("change", function(){
    changeEventAdditionalStatus($(this).val(), $(this).data("id"));
});

$("#eventScLevel").on("change", function(){
    changeScLevel($(this).val());
});

$(".status-button").click(function(e){
    e.preventDefault();
    changeEventStatus($(this).data("id"));   
});

$(".schedule-date-range").change(function(){
        var $form = $(this).closest("form");
        data = $form.serialize();
        $.ajax({
            data: data,
            type: "POST",
            url: "'.Url::to(['/event/save-schedule']).'",

        }).done(function(success) {
              toastr.success("'.Yii::t('app', 'Zapisano').'");
                $("#tab-crew").empty();
                $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
                $("#tab-vehicle").empty();
                $("#tab-vehicle").load("'.Url::to(["event/vehicle-tab", 'id'=>$model->id]).'");
                $("#event-termin").empty().append(success);
                            $("#tab-gear").empty();
            $("#tab-gear").load("'.Url::to(["event/gear-tab", 'id'=>$model->id]).'");
            });
});


');

$this->registerJs('
$( function() {
    $( "#schedule-list").sortable({
    update: function (event, ui) {
        var data = $(this).sortable("serialize");
        $.ajax({
            data: data,
            type: "POST",
            url: "'.Url::to(['/event/schedule-order']).'"
        }).done(function() {
              toastr.success("'.Yii::t('app', 'Zapisano').'");
            $("#tab-crew").empty();
            $("#tab-crew").load("'.Url::to(["event/crew-tab", 'id'=>$model->id]).'");
            $("#tab-vehicle").empty();
            $("#tab-vehicle").load("'.Url::to(["event/vehicle-tab", 'id'=>$model->id]).'");

            })
    }
});
    $( "#schedule-list").disableSelection();
  } );
  ');

$this->registerCss('
    .todo-list .form-group{
        margin: 0;
    }
    .schedule-date-range{
        padding-left:1px;
        padding-right:1px;
    }
    ');