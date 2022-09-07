<?php

use backend\modules\offers\models\OfferExtraItem;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
//use Symfony\Component\VarDumper\VarDumper;
use kartik\tabs\TabsX;

use kartik\form\ActiveForm;
\common\assets\AreYouSureAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$labels = $model->attributeLabels();
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
$currency = $model->priceGroup->currency;

Modal::begin([
    'id' => 'new-model',
    'options' => [
        'tabindex' => false,
    ],
]);
Modal::end();

Modal::begin([
    'id' => 'schedule-modal',
    'header' => Yii::t('app', 'Edytuj harmonogram'),
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
Modal::end();

$this->registerJs('
    $("#add-model").click(function(e){
        e.preventDefault();
        $("#new-model").modal("show").find(".modal-content").load($(this).attr("href"));
    });
    $("#add-model").on("contextmenu",function(){
       return false;
    }); 
    $(".add-schedule").click(function(e){
        e.preventDefault();
        $("#schedule-modal").find(".modal-body").empty();
        $("#schedule-modal").modal("show").find(".modal-body").load($(this).attr("href"));
    });
    $(".add-schedule").on("contextmenu",function(){
       return false;
    }); 
');

?>
<div class="offer-view">

<div class="row">
<div class="col-xs-12">
    <div class="ibox float-e-margins">
    <div class="ibox-content">

    <div class="post-tools col-xs-8">
            <?php
            
        if ($user->can('menuOffersEdit')) {
            echo Html::a(Yii::t('app', 'Magazyn'), ['/warehouse/assign', 'id' => $model->id, 'type' => 'offer'], ['class' => 'btn btn-success btn-sm']);
        } ?>
        <?= Html::a(Yii::t('app', 'Magazyn zewn.'), ['/outer-warehouse/assign', 'id'=>$model->id, 'type'=>'offer'], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Flota'), ['/offer/default/assign-vehicle', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Obsługa'), ['/offer/role/assign', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Inne'), ['/offer/default/offer-custom-items', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']); ?>
        <?= Html::a(Yii::t('app', 'Wyśli E-mailem'), ['/offer/default/send-mail', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." "; ?>
        <?php
        echo Html::a(Yii::t('app', 'PDF'), ['/offer/default/pdf', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
        echo Html::a(Yii::t('app', 'XLS'), ['/offer/default/excel', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
        ?>
        <?php 	if(isset($model->event_id)){
        			echo Html::a(Yii::t('app', 'Zobacz Event'), ['/event/view', 'id'=>$model->event_id], ['class'=>'btn btn-primary btn-sm']);
        		} else if (isset($model->rent_id)) {
                    echo Html::a(Yii::t('app', 'Zobacz Wypożyczenie'), ['/rent/view', 'id'=>$model->rent_id], ['class'=>'btn btn-primary btn-sm'])." ";
        		}
            if ($user->can('eventsEventAdd')){
                if ((!isset($model->event_id))&&(!isset($model->rent_id)))
                {
                    echo Html::a(Yii::t('app', 'Stwórz event'), ['/event/create', 'offer_id' => $model->id], ['class' => 'btn btn-success btn-sm'])." ";

                    echo Html::a(Yii::t('app', 'Dodaj do eventu'), ['/offer/default/add-to-events', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." ";

                }
            }
            if ($user->can('eventRentsAdd')){
                if ((!isset($model->event_id))&&(!isset($model->rent_id)))
                {
                    echo Html::a(Yii::t('app', 'Stwórz wypożyczenie'), ['rent', 'id' => $model->id], ['class' => 'btn btn-success btn-sm'])." ";
                    echo Html::a(Yii::t('app', 'Dodaj do wypożyczenia'), ['/offer/default/add-to-rent', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm']);

                }
            }
         
        ?>
    </div>
    <div class="post-tools col-xs-4">
    <?php
            if ($user->can('menuOffersViewDuplicate')) {
                echo Html::a(Yii::t('app', 'Duplikuj'), ['duplicate', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm pull-right'])." ";
            }
            if ($user->can('menuOffersEdit')) {
                echo Html::a(Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm pull-right']). " ";
            }
            if ($user->can('menuOffersDelete')) {
                echo Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-sm pull-right',
                    'data' => [
                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                        'method' => 'post',
                    ],
                ])." ";
            }
             if ($user->can('menuOffersEdit')) {
            echo Html::a("<i class='fa fa-lock'></i> ".Yii::t('app', 'Zablokuj'), ['/offer/default/block', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm pull-right'])." ";
        } 
            ?>
    </div>
    <div class="clearfix"></div>
    <hr>

    <div class="pdf_box">
    <h2><?php echo Yii::t('app', 'Nazwa projektu: ').$model->name; ?></h2>
    <div class="row">
    <div class="col-md-5">
                        <div class="ibox">

                        <div class="ibox-content no-padding" style="border:0">
                            
                        <p><strong><?=Yii::t('app', 'Numer oferty') ?>: </strong><span class="label label-warning-light"><?php echo $model->id; ?></span></p>
                        <p><strong><?=Yii::t('app', 'Klient') ?>: </strong><?php echo Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id]); ?></p>
                        <?php if (isset($model->contact)){ ?>
                
                        <p><?=$model->contact->displayLabel ?> <?= Yii::t('app', 'tel.') ?>: <?=$model->contact->phone ?></p>
                        <?php } ?>
                        <p><strong><?=Yii::t('app', 'Data sporządzenia') ?>: </strong><?= $model->offer_date ?></p>
                        <?php if ($model->created_by){?>
                        <p><strong><?=Yii::t('app', 'Przygotował') ?>: </strong><?= $model->creator->displayLabel ?></p>
                        <?php } ?>
                        <?php if ($model->event_start ){ ?>
                        <p><strong><?=Yii::t('app', 'Termin') ?>: </strong><?= substr($model->event_start, 0,10) ?> <?= Yii::t('app', 'do') ?> <?= substr($model->event_end,0,10) ?></p>
                        <?php } ?>
                        <?php if ($model->location_id ){ ?>
                        <p><strong><?=Yii::t('app', 'Miejsce') ?>: </strong><?php echo Html::a($model->location->name, ['/location/view', 'id'=>$model->location_id]); ?></p>
                        <?php } ?>
                        <div class="row">
                        <div class="col-md-6">
                        <p><strong><?=Yii::t('app', 'Status') ?>: </strong><?php echo Html::dropDownList('status', $model->status, \common\models\Offer::getStatusList(), ['id'=>'offerStatus']); ?></p>
                        </div> 
                        <div class="col-md-6">
                        <p><strong><?=Yii::t('app', 'Budżet') ?>: </strong><?php echo Html::input('text', 'budget', $model->budget, ['id'=>'offerBudget']); ?></p>
                        </div> 
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                        <p><strong><?=Yii::t('app', 'Zaliczka') ?>: </strong><?php echo Html::input('text', 'pm_cost', $model->pm_cost, ['id'=>'offerPMCost']); ?></p>
                        </div> 
                        <div class="col-md-6">
                        <p><strong><?=Yii::t('app', 'Zaliczka %') ?>: </strong><?php echo Html::input('text', 'pm_cost_percent', $model->pm_cost_percent, ['id'=>'offerPMCostPercent']); ?></p>
                        </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Wartość') ?>: </strong><br/><span class="label label-success"><?php 
                                    echo $formatter->asCurrency($model->getEndValue(), $currency); ?> </span>
                                    <?php
                                    if ($currency!=Yii::$app->settings->get('defaultCurrency', 'main'))
                                    {
                                        echo "<br/><span class='label label-success'>(".$formatter->asCurrency($model->getEndValue()*$model->exchange_rate).")</span>";
                                    }
                                    ?>
                                    
                                </p>
                            </div>
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Koszty') ?>: </strong><br/><span class="label label-danger"><?php echo $formatter->asCurrency($model->cost); ?></span></p>
                            </div>
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Zaliczka') ?>: </strong><br/><span class="label label-warning"><?php
                            if (($model->pm_cost)&&($model->pm_cost>0)){
                                if ($model->value>0)
                                    echo $formatter->asCurrency($model->pm_cost, $currency). " (".round($model->pm_cost/$model->value*100 ,0)."%)"; 
                            }else{
                            if ($model->pm_cost_percent)
                            {
                                echo $formatter->asCurrency($model->pm_cost_percent*$model->getEndValue()/100, $currency). " (".round($model->pm_cost_percent,0)."%)";
                            }else{
                                echo "-";
                            }

                            
                            } ?>
                            </span></p></div>
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Budżet prod') ?>: </strong><br/><span class="label label-success"><?php 
                                    echo $formatter->asCurrency($model->getTotalProductionBudget(), $currency);
                                    ?>
                                    
                                </span></p>
                            </div> 
                            </div>
                        <div class="row">
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Zysk') ?>: </strong><br/><span class="label label-info"><?php
                                    if ($currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                                            echo $formatter->asCurrency($model->getEndValue()*$model->exchange_rate-$model->cost); 
                                    }else{
                                        echo $formatter->asCurrency($model->getEndValue()-$model->cost, $currency); 
                                    }
                                    
                                    ?></span></p>
                            </div>                            
                            <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Prowizje') ?>: </strong><br/><?=$model->getProvisionsSumButton()['b']?></p>
                            </div>                                                    
                             <div class="col-md-3">
                            <p><strong><?=Yii::t('app', 'Zysk po prowizji') ?>: </strong><br/><span class="label label-primary"><?php
                                    if ($currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                                        echo $formatter->asCurrency($model->getEndValue()*$model->exchange_rate-$model->cost-$model->getProvisionsSumButton()['sum']); 
                                    }else{
                                        echo $formatter->asCurrency($model->getEndValue()-$model->cost-$model->getProvisionsSumButton()['sum'], $currency); 
                                    }
                                    
                                    ?></span></p>
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
                    
                        <?= $model->manager->getUserPhoto('img-circle circle-border m-b-md')?>
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
    </div>
    <div class="col-md-3">
                        <div class="ibox">
                        <div class="ibox-content no-padding" style="border:0">   
                        <h3>
                        <?php echo Yii::t('app', 'Harmonogram'); ?>
                        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj etap'), ['/offer/default/add-schedule', 'id' => $model->id], [
                                                            'class' => 'btn btn-xs  btn-success add-schedule ',
                                                            
                                                        ])
                                                        ?>
                        </h3>   
                                <ul class="todo-list ui-sortable" id="list">
                                <?php foreach ($model->offerSchedules as $schedule){ ?>
                                <li class="checklist-item" draggable="true" id="bigitem-<?=$schedule->id?>" style="padding:1px;">
                                <div class="row">
                                <div class="col-xs-12">
                                                                <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', ['/offer/default/update-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-xs  add-schedule',
                                                            
                                                        ])
                                                        ?>
                                                        <?= Html::a('<i class="fa fa-trash"></i>', ['/offer/default/delete-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-danger btn-xs',
                                                            'data' => [
                                                                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                                                'method' => 'post',
                                                            ],
                                                        ])
                                                        ?>
                                </div>
                                <?php
                                $form = ActiveForm::begin([
                                    'enableAjaxValidation' => false,
                                    'enableClientScript' => false,
                                ]);
                                echo $form->field($schedule, 'id')->hiddenInput()->label(false);
                                    echo $form->field($schedule, 'dateRange')->widget(\common\widgets\DateRangeField::className(), ['options'=>[ 'id'=>'s'.$schedule->id, 'class' => 'form-control schedule-date-range', 'autocomplete'=>"off"]])->label($schedule->name);

                                ActiveForm::end();
                                    ?>
                                

                                </div>
                                </div>
                                </li>
                                <?php } ?>
                                </ul>
                        </div>
                        </div>    
    </div>
    </div>
    <div class="row">
    <div class="col-md-12">
        <div class="tabs-container">
            <?php
            $tabItems = [
                [
                    'label'=>$model->name,
                    'content'=>$this->render('_tabOffer', [            
                        'model' =>  $model,
                        'gear_list' => $gear_list,
                        'vehicles' => $vehicles,
                        'settings' => $settings,
                        'skills' => $skills,
                        'users' => $users,
                        'settingAttachmentDataProvider' => $settingAttachmentDataProvider,
                        'offerForm' => $offerForm]),
                    'active'=>true,
                    'options'=> [
                        'id'=>'tab-gear',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Zadania'),
                    'content'=>$this->render('_tabTasks', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-task',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-money"></i> '.Yii::t('app', 'Koszty'),
                    'content'=>$this->render('_tabFinances', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-money',
                    ]
                ],
                [
                    'label'=>'<i class="fa fa-history"></i> '.Yii::t('app', 'Historia'),
                    'content'=>$this->render('_tabHistory', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-history',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Warunki zamówienia'),
                    'content'=>$this->render('_tabRules', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-rules',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Notatki'),
                    'content'=>$this->render('_tabNotes', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab-notes',
                    ]
                ],
            ];


            echo TabsX::widget([
                'items'=>$tabItems,
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,
            ]);
            ?>
        </div>
    </div>
    </div>  
</div>  
</div>    

<script type="text/javascript">
    function changeStatus(status)
    {
        $.ajax({
            url: '<?=Url::to(['/offer/default/change-status'])?>?id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Status zmieniony')?>');
                                  }
            });
    }

    function changeCost(val, type)
    {
        $.ajax({
            url: '<?=Url::to(['/offer/default/change-cost'])?>?id=<?=$model->id?>'+'&value='+val+'&type='+type,
                    success: function(response){
                        toastr.success('<?=Yii::t('app', 'Zaliczka zmieniona.')?>');
                                  }
            });
    }

    function changeBudget(val)
    {
        $.ajax({
            url: '<?=Url::to(['/offer/default/change-budget'])?>?id=<?=$model->id?>'+'&value='+val,
                    success: function(response){
                        window.location.reload(false);
                                  }
            });
    }
</script>
<?php
$this->registerJs('

$("#offerStatus").on("change", function(){
    changeStatus($(this).val());
});
$("#offerPMCostPercent").on("change", function(){
    changeCost($(this).val(), 1);
});
$("#offerPMCost").on("change", function(){
    changeCost($(this).val(), 2);
});
$("#offerBudget").on("change", function(){
    changeBudget($(this).val());
});

$(".schedule-date-range").change(function(){
        var $form = $(this).closest("form");
        data = $form.serialize();
        $.ajax({
            data: data,
            type: "POST",
            url: "'.Url::to(['/offer/default/save-schedule']).'",

        }).done(function() {
              toastr.success("'.Yii::t('app', 'Zapisano').'");
            });
});


');


$this->registerJs("
$( function() {
    $( '#list').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/offer/default/schedule-order'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  ");
?>

