<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
\common\assets\Gmap3Asset::register($this);
use yii\bootstrap\Modal;
use yii\helpers\Url;



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
        <?php 
        if ($model->type==2)
                $type = 3;
            else
                $type = 1;
        if (Yii::$app->user->can('eventEventEditPencil')) { 
            
            if (isset($model->eventStatut))
                $statuts = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1, 'button'=>1])->andWhere(['>', 'position', $model->eventStatut->position])->all();
            else
                $statuts = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1, 'button'=>1])->all();
            foreach ($statuts as $s)
            {
                $title = $s->name;
                if ($s->icon)
                {
                    $title = '<i class="fa '.$s->icon.'"></i> '.$title;
                }
                echo " ".Html::a($title, ['#'], ['class' => 'btn status-button', 'data-id'=>$s->id, 'style'=>'color:white; background-color:'.$s->color]);
            }
     } ?>
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
                            if ($model->getParentEvent())
                            {
                                echo Yii::t("app", "Zadanie na potrzeby: ").Html::a($model->getParentEvent()->name, ['view', 'id'=>$model->getParentEvent()->id], ['class'=>'btn btn-primary']);
                            }
                        ?>
                        <?php 
                            $teams = $model->getAssignedUsers();
                            if ($teams->getTotalCount()>0){
                        ?>
                            <div class="team-members">
                            <?php  foreach ($teams->getModels() as $team){ ?>
                            <?php if ($team->getPhotoUrl()!="") { ?>
                            <a href="#"><img alt="image" class="img-circle img-small" src="<?php echo $team->getPhotoUrl();?>" title="<?=$team->first_name." ".$team->last_name; ?>"></a>
                            <?php } ?>
                            <?php } ?>
                            </div>
                        <?php } ?>
                                <h4 class="font-bold"> <?= Yii::t('app', 'Klient') ?>: <?php echo Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id]); ?></h4>
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
                                <h4 class="font-bold"> <?= Yii::t('app', 'Termin') ?>: <?php echo Yii::$app->formatter->asDateTime($model->getTimeStart(),'short')." - ".Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short'); ?></h4>
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
                            <p><?= Yii::t('app', 'STATUS') ?>:
                            <?php
                              if ((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent'))) { 
                                echo Html::dropDownList('status', $model->status, \common\models\Event::getStatusList($model->status), ['id'=>'eventStatus']);
                                }else{
                                    echo $model->getStatusButton();
                                }
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
                            <?php  if ($model->info!="") { ?>
                            <h4><?= Yii::t('app', 'Uwagi') ?></h4>
                            <p><?php echo $model->info; ?></p>
                            <?php } ?>

                            <div class="row  m-t-sm">
                                <div class="col-sm-4">
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
    <div class="col-md-4">
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
        <div class="row" style="margin-top:20px;">
                                    <?php $status = $model->getTaskStatus(); ?>
                                <div class="col-lg-12">
                                            <div class="progress progress-striped active m-b-sm">
                                                <div style="width: <?=$status['status']?>%;" class="progress-bar"></div>
                                            </div>
                                            
                                            <small><strong><?=Yii::t('app', 'Status')?></strong> - <?=Yii::t('app', 'wykonano')?> <strong><?=$status['status']?>%</strong>. <?=Yii::t('app', 'Jest to')?> <?=$status['done']?>/<?=$status['task']?> zadań.</small>
                                </div>
                            </div>

    </div>
    <div class="col-md-4">
<div class="row">
    <div class="col-md-12">
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
                            <div class="ibox-content dashboard-400 small-font" style="padding-left:10px; padding-left:10px; height:250px;">
                            <div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->notes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">

                                            <div class="actions pull-right">
                                            <?php if ((!$m->auto)&&($m->user_id==Yii::$app->user->id)) echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            </div>
                                            <strong><?=$m->user->displayLabel?>: </strong><?=$m->text?></br>
                                            <small class="text-navy"><?=$m->datetime?></small></br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/note/add-file', 'id'=>$m->id]); ?></small></br>
                                            <?php foreach ($m->noteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>

                                        </div>
                                        </div>
                                        <?php
                                        }   
                                    ?>                                
                                </div>
                            </div>
                            </div>
                            </div>
                            </div>
    </div>
</div>
        
    </div>
</div>

</div>
        <div class="tabs-container">
            <?php 
            //!!!: Zmiana zakładek -> zmiana indexu w js (google maps)
            
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
                   'label'=>Yii::t('app', 'Załączniki').' <span class="badge badge-primary pull-right">'.count($model->attachments).'</span>',
                    'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                    'visible'=>$user->can('eventEventEditEyeAttachment'),
                    'options'=> [
                        'id'=>'tab-attachment',
                    ]
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
                    'label'=>$model->getConflictCount()." ".$model->getAssignedOuterGearModelsNumber().Yii::t('app', 'Sprzęt zewnętrzny'),
                    'content'=>$this->render('_tabOuterGear', ['model'=>$model]),
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
                if (Yii::$app->session->get('company')==1)
                {
                     $tabItems[] =           [
                                    'label'=>Yii::t('app', 'Oferty'),
                                    'content'=>$this->render('_tabOffers', ['model'=>$model]),
                                    'visible'=>$user->can('eventsEventEditEyeOffer'),
                                    'options'=> [
                                        'id'=>'tab-offer',
                                    ]
                                ];
            }
 $tabItems2 =   [ 
                [
                    'label'=>Yii::t('app', 'Flota'),
                    'content'=>$this->render('_tabVehicle', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeVehicles'),
                    'options'=> [
                        'id'=>'tab-vehicle',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Ekipa'),
                    'content'=>$this->render('_tabUser', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeCrew'),
                    'options'=> [
                        'id'=>'tab-crew',
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
                    'content'=>$this->render('_tabFinances', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeFinance'),
                    'options'=> [
                        'id'=>'tab-finances',
                    ]
                ],
                [
                   'label'=>Yii::t('app', 'Aktualności'),
                    'content'=>$this->render('_tabNotes', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeHistory'),
                    'options'=> [
                        'id'=>'tab-notes',
                    ]
                ],
                [
                   'label'=>Yii::t('app', 'Historia'),
                    'content'=>$this->render('_tabLog', ['model'=>$model]),
                    'visible'=>$user->can('eventsEventEditEyeHistory'),
                    'options'=> [
                        'id'=>'tab-log',
                    ]
                ],
    
            ];
            $tabItems = array_merge($tabItems, $tabItems2);

            if ($model->getTaskFor())
            {
                $tabItems[] = [
                    'label'=>Yii::t('app', 'Zadanie z eventu'),
                    'content'=>$this->render('/task/newview', ['model'=>$model->getTaskFor(), 'alone'=>true]),
                    'options'=> [
                        'id'=>'tab-request',
                    ]
                ];
            }

            if (Yii::$app->params['companyID']=="admin")
            {
                $tabItems[] = [
                    'label'=>Yii::t('app', 'Zgłoszenia'),
                    'content'=>$this->render('_tabRequest', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-request',
                    ]
                ];
            }
            echo TabsX::widget([
                'items'=>$tabItems,
                'id'=>'eventTabs',
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,
            ]);

            ?>

        </div>
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
</script>

<?php
 $this->registerJs("
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

$('#eventTabs li a').click(function(e){
    $('.task-schema-details').empty();
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
</script>
<?php
$this->registerJs('

$("#eventStatus").on("change", function(){
    changeEventStatus($(this).val());
});

$(".eventStatus").on("change", function(){
    changeEventAdditionalStatus($(this).val(), $(this).data("id"));
});

$(".status-button").click(function(e){
    e.preventDefault();
    changeEventStatus($(this).data("id"));
});


');