<?php
/* @var $this \yii\web\View */
/* @var $event \common\models\Event */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\models\GearService;
use common\models\Rent;
use common\models\RentGearItem;
use common\components\grid\GridView;
use common\models\EventGearItem;
use common\models\GearItem;
use common\models\Event;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
$user = Yii::$app->user;
$warehouses = \common\models\Warehouse::find()->all();
use kop\y2sp\ScrollPager;
use backend\modules\permission\models\BasePermission;

use kartik\form\ActiveForm;
use yii\widgets\Pjax;
/*
\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);
*/

if ($conflict)
{
    $conflictModel = \common\models\EventConflict::findOne($conflict);
    ?>
        
    <div class="conflict-modal widget style1 navy-bg">
    <div class="row">
                        <div class="col-md-2">
                            <i class="fa fa-info fa-3x"></i> 
                        </div>
                        <div class="col-md-10">
                            <?=Yii::t('app', 'Rozwiązujesz konflikt na sprzęt ').$conflictModel->gear->name.Yii::t('app', ' brakuje ').$conflictModel->quantity.Yii::t('app', ' szt.')?>
                        </div>
                    </div>
    
    
    </div>
    <?php
}
$packlist = \common\models\Packlist::findOne($packlist);
$packlists = \common\helpers\ArrayHelper::map(\common\models\Packlist::find()->where(['event_id'=>$event->id])->andWhere(['IS NOT', 'start_time', null])->andWhere(['blocked'=>0])->asArray()->all(), 'id', 'name');

$this->title = Yii::t('app', 'Przypisz sprzęt').' - ' . $event->name;

Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Sprzęt powiązany')."</h4>",
    'id' => 'connected_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Konflikty do rozwiązania')."</h4>",
    'id' => 'conflicts_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Brak dostępnych egzemplarzy')."</h4>",
    'id' => 'similar_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]

]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>

<?php // $this->render('_summaryTable', ['event'=>$event->id, 'type' => $type]) ?>
    <div class="gear gears row">
        <div class="ibox float-e-margins">
        <?php
        $datetime1 = date_create($event->getTimeStart()); 
        $datetime2 = date_create($event->getTimeEnd());
        $interval = date_diff($datetime1, $datetime2);
        if ($interval->y>=1)
        { ?>
<div class="alert alert-danger">
                              <?=  Yii::t('app', 'UWAGA!!! dla wydarzeń dłuższych niż rok nie działa sprawdzanie dostępności sprzętu.') ?>
                            </div>
     <?php   }
            ?>
                <div class="ibox-title newsystem-bg">
                    <h4><?php echo $this->title; ?></h4>
                </div>
<div class="ibox-content">
<div class="menu-pils">
    <?= $this->render('_categoryMenu'); ?>
</div>
<div class="warehouse-container" style="margin-top:10px;">
            <?php if ($conflict)
                    echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'), [$this->context->returnRoute, 'id'=>$event->id, "#"=>"eventTabs-dd3-tab2"], ['class'=>'btn btn-primary btn-sm btn-return']);
                else
                    echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'), [$this->context->returnRoute, 'id'=>$event->id, "#"=>"tab-gear"], ['class'=>'btn btn-primary btn-sm btn-return']); ?>
            <?php if ($type=="event"){ echo Html::a(Yii::t('app', 'Magazyn zewnętrzny'), array_merge(['outer-warehouse/assign'], $_GET), ['class'=>'btn btn-success btn-sm']); } ?>
    <div class="row">
        <div class="col-md-3">
                <div class="search-form">
                <?php echo Html::beginForm(Url::current(['to_date'=>null, 'from_date'=>null, 'q'=>null]), 'get', ['class'=>'form-inline']); ?>

            <div class="form-group">
                <?php echo Html::textInput('q', $warehouse->q, ['placeholder'=>Yii::t('app', 'Szukaj'), 'class'=>'form-control']); ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><?= Yii::t('app', 'Szukaj') ?></button>
            <?php echo Html::endForm(); ?>
        </div>

        </div>
        <div class="col-md-3">
                <?= kartik\widgets\Select2::widget([
                                'data' => $packlists,
                                'value'=>$packlist->id,
                                'name' => 'packlists',
                                'id' => 'select-packlist',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz packlistę...'),
                                    'id'=>'select-packlist',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => false,
                                ],
                            ]); ?>
        </div>
        <div class="col-md-6">
        <div class="row">
        <div class="col-md-6">
        <?php

            echo $packlist->getScheduleDiv();

        ?>
        </div>
        <div class="col-md-6">
        <input type="text" id="js-range-slider-packlist" data-start="<?=substr($packlist->start_time, 0, 16)?>" data-end="<?=substr($packlist->end_time, 0, 16)?>" name="range" value="0;10"/>
        <input type="hidden" id="warehouse_start" name="warehouse_start" value="<?=$packlist->start_time?>"/>
        <input type="hidden" id="warehouse_end" name="warehouse_end" value="<?=$packlist->end_time?>"/>
        </div>
        </div>
        </div>
    </div>


        <?php

        echo $this->render('_sets', ['gearSet'=>$gearSets, 'event'=>$event, 'type'=>$type, 'category'=>$category, 'packlist'=>$packlist]);

        $gearColumns = [
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) use ($warehouse) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return Html::a(Html::icon(' fa fa-calendar'), ['/gear/calendar', 'id'=>$model->id, 'start'=>$warehouse->from_date, 'end'=>$warehouse->to_date], ['class'=>'calendar-button btn btn-xs btn-success', 'data-id'=>$model->id])." ".$content;
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Zarezerwuj'),
                'format' => 'raw',
                'value' => function ($model) use ($warehouse, $assignedModels, $event, $type) {
                    $content = '';
                    //if ($model->no_items != 0) {
                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignmentPacklist();
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = key_exists($model->id, $assignedModels) ? $assignedModels[$model->id] : 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $isAvailable = $model->getAvailabe($event->getTimeStart(), $event->getTimeEnd());
                        //if ($assignmentForm->oldQuantity>0)
                            $isAvailable = true;

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form',
                            ],
                            'action' =>['assign-gear', 'id'=>$event->id, 'type'=>$type],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        echo Html::activeHiddenInput($assignmentForm, 'oldQuantity');
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'disabled'=> $isAvailable ? false : true,
                            'style' => 'width: 52px;',
                            'class'=>'gear-quantity',
                        ]);
                        ActiveForm::end();
                        $this->endBlock();

                        $content .= $this->blocks['form'];
                    //}
                    return $content;
                }
            ],

            [
                //'attribute'=>'available',
                'label'=>Yii::t('app', 'Dostępnych'),
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'format' => 'raw',
                'value'=>function($gear, $key, $index, $column) use ($warehouse, $assignedModels)
                {
                    //$assigned = key_exists($gear->id, $assignedModels) ? $assignedModels[$gear->id] : 0;
                    if ($gear->type!=1)
                    {
                        return $gear->quantity;
                    }
                    $assigned = 0;
                    if ($gear->no_items)
                    {
                        
                        $serwisNumber = $gear->getNoItemSerwis();
                        $serwis = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        $total = ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber)+$assigned ;
                        return "<div class='quantity-div' data-gearid=".$gear->id.">".$total. " " . $serwis."</div>";
                    }
                    else
                    {
                        $serwisNumber = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_SERVICE])->count();
                        $needSerwis = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_NEED_SERVICE])->count();

                        $serwis = null;
                        $need = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        if ($needSerwis > 0) {
                            $need = Html::tag('span', Yii::t('app', 'Wymaga serwisu').': ' . $needSerwis, ['class' => 'label label-warning']);
                        }
                        $total = ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber)+$assigned ;
                        return "<div class='quantity-div' data-gearid=".$gear->id.">".$total. " " . $serwis." ".$need."</div>";
                    }
                }
            ],
            [
                'label'=>Yii::t('app', 'Na stanie'),
                'format'=>'raw',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'contentOptions'=>['style'=>'white-space:nowrap;'],
                'value'=>function($gear, $key, $index, $column) use ($warehouses)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items)
                    {
                        $return = $gear->quantity;
                        
                    }
                    else
                    {
                        $return = $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                    if ($warehouses)
                    {
                            foreach ($warehouses as $w)
                            {
                                $return .= $w->getNumberLabel($gear);
                            }
                            //$return .="<br/>".Html::a(Yii::t('app', 'Zarządzaj'), ['manage-warehouse', 'gear_id'=>$gear->id], ['class'=>'btn btn-xs btn-primary manage-warehouse']);
                    }
                    return $return;

                }
            ],
            [
                        'label' => Yii::t('app', 'Pakowany'),
                        'format'=>'raw',
                        'value'=> function($model){
                            $result = "";
                            foreach ($model->getPacking() as $case){
                                $result .= $case."</br>";
                            }
                            return $result;
                        }
            ],
            [
                'label' => Yii::t('app', 'Zarezerwowany'),
                'format' => 'raw',
                'visible'=> ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL])),
                'value' => function ($model) use ($warehouse, $user) {
                                        $return = "";
                    if ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL]))
                    {
                    $working = $model->getEvents($warehouse->from_date, $warehouse->to_date);
                    $workingNear = $model->getEventsNear($warehouse->from_date, $warehouse->to_date);
                    $result = "";
                    foreach ($working['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " <span class='label label-primary' style='background-color:".$eventGear->packlist->color."'>".$eventGear->quantity."</span><br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($working['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " <span class='label label-primary' style='background-color:".$eventGear->packlist->color."'>".$eventGear->quantity."</span><br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                        
                    }
                    foreach ($workingNear['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    return "<div class='bookings' data-gearid=".$model->id.">".$result."</div>";
                    }else{
                        return "-";
                    }
                }
            ],
            'price',
                        [
                    'header' => Yii::t('app', 'Uwagi'),
                    'format' => 'html',
                    'contentOptions' => function ($model) {
                        if ($model->getItemsInfo()!="")
                            return ['style'=>'background-color:#ed5565; color:white; white-space:nowrap; cursor_pointer;', 'class' => 'info'];
                        else
                            return [];
                    },
                    'value' => function ($model) {
                                $info = $model->getItemsInfo();
                                if ($info!="")
                                {
                                     $info_div ="<div class='display_none'>".$info."</div><span>".Yii::t('app', 'Pokaż uwagi')."</span>";
                                    return $info_div;                                   
                                }else{
                                    return "";
                                }

                    },                   
            ],
            ];
        ?>
        <div class="panel_mid_blocks">
            <div class="panel_block">
                <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $warehouse->getGearDataProvider(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap',
                'id'=>'assign-gear-grid'
            ],
            'filterModel' => null,
            'columns' => $gearColumns,
                                'pager' => [
            'class'     => ScrollPager::className(),
            'container' => '.grid-view tbody',
            'item'      => 'tr',
            'paginationSelector' => '.grid-view .pagination',
            'eventOnRendered' => 'function() {
                    changeQuantityForm();
            }',
            'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            'enabledExtensions'  => [
                ScrollPager::EXTENSION_SPINNER,
                //ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
        ],
        ]); ?>
            </div>
        </div>
    </div>
    </div>

</div>
<div id="gear-list">
    <a class="open-gear-list" title="<?=Yii::t('app', 'Pokaż zarezerwowany sprzęt')?>"><i class="fa fa-plug"></i></a>
</div>
<?php
Modal::begin([
    'header' => Yii::t('app', 'Lista sprzętu'),
    'id' => 'gear_list_modal',
    'class'=>'inmodal'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<?php
$c = Yii::$app->getRequest()->getQueryParam('c');
$s = Yii::$app->getRequest()->getQueryParam('s');
$eventGearCatUrl = Url::to(['warehouse/assign', 'id'=>$event->id, 'type'=>$type, 'c'=>$c, 's'=>$s]);

$eventGearConnectedUrl = Url::to(['warehouse/assign-gear-connected', 'id'=>$event->id, 'type'=>$type, 'packlist'=>$packlist->id]);
$eventGearOuterConnectedUrl = Url::to(['outer-warehouse/manage-gear-connected', 'event_id'=>$event->id, 'packlist'=>$packlist->id]);
$checkGearConflictsUrl = Url::to(['warehouse/gear-conflicts', 'event_id'=>$event->id]);
$eventGearConflictsUrl = Url::to(['warehouse/gear-conflicts-modal', 'event_id'=>$event->id]);
$eventGearSimilarUrl = Url::to(['warehouse/gear-similar', 'id'=>$event->id, 'packlist'=>$packlist->id]);
$saveSimilarUrl = Url::to(['warehouse/save-similar', 'id'=>$event->id, 'packlist_id'=>$packlist->id]);
$saveConflictUrl = Url::to(['warehouse/save-conflict', 'id'=>$event->id, 'packlist_id'=>$packlist->id]);
$eventGearUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearGroupCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventGroupUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
$eventGearQuantityUrl = Url::to(['warehouse/assign-gear-packlist', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1, 'packlist'=>$packlist->id]);
$reloadUrl = Url::to(['warehouse/reload-quantity', 'packlist'=>$packlist->id]);
$bookingsUrl = Url::to(['warehouse/reload-bookings', 'packlist'=>$packlist->id]);
$eventGearModelUrl = Url::to(['gear/get-gear-as-json']);
$eventGearList = Url::to(['warehouse/get-assigned-gear', 'event_id'=>$event->id, 'type'=>$type]);
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}

    .manage-crew-div{float:left; border:1px solid white; padding-left:5px;}

    .manage-crew-div input[type="checkbox"] {
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

    function changeQuantityForm(){
        $(".gear-quantity").unbind("change");
        $(".gear-quantity").unbind("input");
        $(".gear-quantity").on("input", function(e){
            $(".btn-return").hide();
        });
        $(".gear-quantity").on("change", function(e){
            $(".btn-return").show();
            $(".btn-return").prop("disabled", true);
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    start = $("#warehouse_start").val();
    end = $("#warehouse_end").val();
    var value = $(this).val();
    var oldValue = form.find("#gearassignmentpacklist-oldquantity").val();
    var gear_id = form.find("#gearassignmentpacklist-itemid").val();
    $(".gear-quantity").prop("disabled", true);
    $.post("'.$eventGearQuantityUrl.'&start="+start+"&end="+end, data, function(response){
         $(".gear-quantity").prop("disabled", false);
         $(".btn-return").prop("disabled", false);
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
                /*if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }*/
            //brak wolnych egzemplarzy, wyswietlamy okienko z podobnymi
                showSimilarModal(data, start, end);
        }
        else
        {
            form.find("#gearassignmentpacklist-oldquantity").val(value);
            if (value>0)
            {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
                if ((response.connected.length)||(response.outerconnected.length))
                {
                    showConnectedModal(response.connected, response.outerconnected);
                }
                resolveConflict(value, oldValue);
            }
            else{
                toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");

            }
            if (value<oldValue)
            {
                $.post("'.$checkGearConflictsUrl.'&gear_id="+gear_id, data, function(response){
                    if (parseInt(response.conflicts)>0)
                    {
                        number = oldValue-value;
                        showConflictsToResolveModal(gear_id, number);
                    }
                });
            }
        }
        $(".gear-assignment-form").yiiActiveForm("updateAttribute", "gearassignment-quantity", error);
        
    });
   
    
   return false;
});
    $(".gear-assignment-form").submit(function(e){
        e.preventDefault();
    });
    $(".info").unbind("click");
    $(".calendar-button").unbind("click");

$(".info").click(function(){
    $(this).find("span").first().toggleClass("display_none");

    var ourDiv = $(this).find("div").first();
    if (ourDiv.hasClass("display_none")) {
        ourDiv.slideDown();
    }
    else {
        ourDiv.slideUp();
    }
    ourDiv.toggleClass("display_none");
});

$(".calendar-button").click(function(e){
    e.preventDefault();
    wname = "kalendarz"+ $(this).attr("data-id");
    window.open($(this).attr("href"), wname ,"height=500,width=850");
});


    }
changeQuantityForm();
$(".open-gear-list").on("click", function(){ 
    openGearModal();
})

function openGearModal(){
    var modal = $("#gear_list_modal");
    modal.find(".modalContent").empty();
    modal.find(".modalContent").append("'.$spinner.'");
    modal.find(".modalContent").load("'.$eventGearList.'");
    modal.modal("show");
}




');
?>

<?php //Pjax::end(); ?>
<?php
//    echo Dialog::widget([
//        'options'=> [
//            'message'=>'--form--',
//        ],
//    ]);
//
$this->registerJs('

$(".set-custom-dates").on("click", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    var modal = $(this).closest("[role=\"dialog\"]");
    var btn = $(this);
    var itemId = btn.data("itemid");
    $.post("'.$eventGearUrl.'", data, function(response){
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
            $.notify({
                message : error,
                icon: "glyphicon glyphicon-remove",
            }, {
                type:"danger",
                z_index: 5000,
                delay:3000,
            });
            
            
        }
        else
        {
            modal.modal("hide");
            $.get("", {},function(r){
                 var c = $(r).find("#custom_date_range-button-"+itemId).html();
                 $("#custom_date_range-button-"+itemId).html(c);
                 $.notify({
                    message : "'.Yii::t('app', 'Zapisano').'",
                    icon: "glyphicon glyphicon-ok",
                }, {
                    type:"success",
                    z_index: 5000,
                    delay:3000,
                });
            });
        }
    });
    return false;
});

$(".remove-custom-dates").on("click", function(e) {
    e.preventDefault();
    var modal = $(this).closest("[role=\"dialog\"]");
    var data = $(this).data();
    data.itemId=data.itemid;
    $.post("'.$eventGearUrl.'", data, function(response){
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
            $.notify({
                message : error,
                icon: "glyphicon glyphicon-remove",
            }, {
                type:"danger",
                z_index: 5000,
                delay:3000,
            });
        }
        else
        {
            var itemId = data.itemId;
            modal.modal("hide");
            $.get("", {},function(r){
                 var c = $(r).find("#custom_date_range-button-"+itemId).html();
                 $("#custom_date_range-button-"+itemId).html(c);
                 $.notify({
                    message : "'.Yii::t('app', 'Usunięto').'",
                    icon: "glyphicon glyphicon-ok",
                }, {
                    type:"success",
                    z_index: 5000,
                    delay:3000,
                });
            });
           
        }
    });

    return false;
});

');
?>

<?php
$this->registerJs('
var currentEl;
$(document).on("pjax:beforeReplace", function(event, contents, options) {
var key = $(event.relatedTarget).closest("tr").data("key");
  currentEl = contents.find("tr[data-key="+key+"]") ;

  var el = currentEl.next("tr").find(".wrapper");
  
  if (el)
  {
    el.hide();
  }

});
$(".arrow-up").on("click", function(){
    $(this).closest("tr").next("tr").find(".wrapper").slideUp(200);
});
$(document).on("pjax:complete", function() {
  var el = currentEl.next("tr").find(".wrapper");
  if (el) 
  {
    el.slideDown();
  }
  
})
');

$this->registerCss('

.container-working-time button { color: red; }
');

$this->registerJs('

    $(".schedule-checkbox-packlist").click(function(e){
        start = "'.substr($event->event_end, 0, 16).'";
        end = "'.substr($event->event_start, 0, 16).'";
        $("#schedule-box").find(".schedule-checkbox-packlist").each(function(){
            if ($(this).prop("checked"))
            {
                if ($(this).data("start")<start)
                {
                    start = $(this).data("start");
                }
                if ($(this).data("end")>end)
                {
                    end = $(this).data("end");
                }
            }
        });
        if (start<=end)
        {
            $("#js-range-slider-packlist").ionRangeSlider("update", {
                from: tvalues.indexOf(start),
                to: tvalues.indexOf(end)
                });
            $("#warehouse_start").val(start);
            $("#warehouse_end").val(end);
            }else
            {
            $("#js-range-slider-packlist").ionRangeSlider("update", {
                from: tvalues.indexOf(end),
                to: tvalues.indexOf(start)
                });
                $("#warehouse_start").val(end);
                $("#warehouse_end").val(start);
            }
            reloadAvability();


    });


        $("#js-range-slider-packlist").ionRangeSlider({
                type: "double",
                min:0,
                max: tvalues.length,
                from: tvalues.indexOf($("#js-range-slider-packlist").data("start")),
                to: tvalues.indexOf($("#js-range-slider-packlist").data("end")),
                values: tvalues,
                onFinish: function (data) {
                                $("#warehouse_start").val(data.fromValue);
                                $("#warehouse_end").val(data.toValue);
                                reloadAvability();
                },
            });
');

$this->registerJs('




$("#select-packlist").change(function(){
    location.href = "'.$eventGearCatUrl.'&packlist="+$(this).val();
});




$(".gear-assignment-form").on("keyup keypress", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 
    e.preventDefault();
    return false;
  }
});
');
$this->registerCss('
    .display_none {display: none;}
');

$this->registerCss("
    .conflict-modal{
        position:fixed;
        right:0px;
        top:400px;
        width:250px;
        background-color: #1ab394;
        font-size: 13px;
        color: white;
        padding-right:15px;
        padding-left:15px;
        z-index:2000;
    }");
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function resolveConflict(newValue, oldValue){
        <?php if ($conflict) { ?>
        swal({
            closeOnClickOutside: false,
            title: "<?=Yii::t('app', 'Czy konflikt został rozwiązany?')?>",
            icon:"info",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            partial: {
                text:"<?=Yii::t('app', 'Częściowo')?>",
                value:"partial"
            },
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              location.href = "<?=Url::to(['warehouse/conflict', 'id'=>$conflict]);?>";
              break; 
            case "partial":
                    $.post("<?=Url::to(['warehouse/conflict-partial', 'id'=>$conflict]);?>&old="+oldValue+"&quantity="+newValue, {}, function(response){
                            toastr.success("<?=Yii::t('app', 'Konflikt zedytowany')?> ");
                    });
              break;       
          }
        });
        <?php } ?>
    }
        function reloadAvability()
        {
            $(".quantity-div").each(function(){
                $(this).empty();
                gear_id = $(this).data("gearid");
                start = $("#warehouse_start").val();
                end = $("#warehouse_end").val();
                data = [];
                $(this).append("<?=$spinner?>");
                var qdiv = $(this);
                $.post("<?=$reloadUrl?>"+"&gear_id="+gear_id+"&start="+start+"&end="+end, data, function(response){
                qdiv.empty();
                qdiv.append(response); 
                }); 
            });
            $(".bookings").each(function(){
                $(this).empty();
                gear_id = $(this).data("gearid");
                start = $("#warehouse_start").val();
                end = $("#warehouse_end").val();
                data = [];
                $(this).append("<?=$spinner?>");
                var bdiv = $(this);
                $.post("<?=$bookingsUrl?>"+"&gear_id="+gear_id+"&start="+start+"&end="+end, data, function(response){
                bdiv.empty();
                bdiv.append(response); 
                }); 
            });
        }
        function showConnectedModal(gears, outergears){
        var modal = $("#connected_modal");
        modal.find(".modalContent").empty();
        var content = "<table class='table'><thead><tr><th>#</th><th>Nazwa</th><th>Liczba sztuk</th><th>Dostępnych</th></tr></thead><tbody>";
        for (var i=0; i<gears.length; i++)
        {
            if (gears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-connectedcheckbox\'  data-gearid=\'"+gears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+gears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+gears[i].count+"'/></td><td class='connected-avability' data-gearid="+gears[i].id+"></td></tr>";
        }
        for (var i=0; i<outergears.length; i++)
        {
            if (outergears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-outerconnectedcheckbox\'  data-gearid=\'"+outergears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+outergears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+outergears[i].count+"'/></td><td>Zewn.</td></tr>";
        }
        content += "</tbody></table>";
        content += '<div class="row"><div class="pull-right"><a class="btn btn-primary add-connected-button" href="#">Dodaj</a> ';
        content += '<a class="btn btn-default close-connected-button" href="#">Anuluj</a></div></div>';
        modal.find(".modalContent").append(content);        
        modal.modal("show");
        $(".add-connected-button").click(function(){ saveConnected();})
        $(".close-connected-button").click(function(){  $("#connected_modal").modal("hide");})
        $(".connected-avability").each(function(){
            $(this).empty();
                gear_id = $(this).data("gearid");
                start = $("#warehouse_start").val();
                end = $("#warehouse_end").val();
                data = [];
                $(this).append("<?=$spinner?>");
                var qdiv = $(this);
                $.post("<?=$reloadUrl?>"+"&gear_id="+gear_id+"&start="+start+"&end="+end, data, function(response){
                qdiv.empty();
                qdiv.append(response);
                }); 
        });
        }

        function showSimilarModal(data, start, end){
            <?php if ($type=='event'){ ?>
            var modal = $("#similar_modal");
            modal.find(".modalContent").empty();
            $.post("<?=$eventGearSimilarUrl?>"+"&start="+start+"&end="+end, data, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
            });        
            <?php } ?>
        
        }

        function showConflictsToResolveModal(gear_id, number){
            var modal = $("#conflicts_modal");
            modal.find(".modalContent").empty();
            data = [];
            $.post("<?=$eventGearConflictsUrl?>&gear_id="+gear_id+"&number="+number, data, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
            });        
        
        }

        function bookSimilars(){
            start = $("#warehouse_start").val();
            end = $("#warehouse_end").val();
            $.post('<?=$saveSimilarUrl?>&start='+start+'&end='+end, $("#similarForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                                $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignmentpacklist-quantity").first().val(response.responses[i].total);
                            if (response.responses[i].success==1)
                            {
                                toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                            }
                            else
                            {
                                 var error = [response.responses[i].error];
                                toastr.error(response.responses[i].name+" "+error);                               
                            }

                        }
                    }
                if ((response.connected.length)||(response.outerconnected.length))
                {
                    showConnectedModal(response.connected, response.outerconnected);
                }
                $("#similar_modal").modal("hide");
            });

        }

        function bookConflicts(){
            start = $("#warehouse_start").val();
            end = $("#warehouse_end").val();
            $.post('<?=$saveConflictUrl?>&start='+start+'&end='+end, $("#conflictForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                            $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignmentpacklist-quantity").first().val(response.responses[i].total);
                            if (response.responses[i].success==1)
                            {
                                toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                            }
                            else
                            {
                                 var error = [response.responses[i].error];
                                toastr.error(response.responses[i].name+" "+error);                               
                            }

                        }
                if ((response.connected.length)||(response.outerconnected.length))
                {
                    showConnectedModal(response.connected, response.outerconnected);
                }
                    }
                $("#similar_modal").modal("hide");
            });
        }


     function saveConnected()
     {
        $("#connected_modal").find('.gear-connectedcheckbox').each(function(){
            if ($(this).is(":checked"))
            {
            var gear_id = $(this).data('gearid');
            var quantity = $(this).parent().parent().find('.gear-connectedinput').first().val();
            var start = $("#warehouse_start").val();                
            var end = $("#warehouse_end").val();
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity
                }
                $.post("<?=$eventGearConnectedUrl?>&start="+start+"&end="+end, data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                                    $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignmentpacklist-quantity").first().val(response.responses[i].total);
                                    
                                }
                                else
                                {
                                    var error = [response.responses[i].error];
                                    toastr.error(response.responses[i].name+" "+error);                               
                                }

                            }
                        }                
                });
            }
               
            }

        });
        $("#connected_modal").find('.gear-outerconnectedcheckbox').each(function(){
            if ($(this).is(":checked"))
            {
            var gear_id = $(this).data('gearid');
            var quantity = $(this).parent().parent().find('.gear-connectedinput').first().val();
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity
                }
                $.post("<?=$eventGearOuterConnectedUrl?>", data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                                }
                                else
                                {
                                    var error = [response.responses[i].error];
                                    toastr.error(response.responses[i].name+" "+error);                               
                                }

                            }
                        }                
                });
            }
               
            }

        });

        $("#connected_modal").modal("hide");
     }   

</script>
<script type="text/javascript">
    var tvalues = [];
        tvalues = [<?php $date = new DateTime($event->event_start); 
        $interval = DateInterval::createFromDateString('1 day');
        $date->sub($interval);
        $date2 = new DateTime($event->event_end); 
        $date2->add($interval);
        while($date->format('Y-m-d H:i')<=$date2->format('Y-m-d H:i')){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));}  ?> ];
</script>
