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
use kop\y2sp\ScrollPager;
use backend\modules\permission\models\BasePermission;

$user = Yii::$app->user;
$warehouses = \common\models\Warehouse::find()->all();

use kartik\form\ActiveForm;
use yii\widgets\Pjax;
\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);

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
<?php
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
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                <div class="ibox" style="margin-top:10px">
            <?php if ($conflict)
                    echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'), [$this->context->returnRoute, 'id'=>$event->id, "#"=>"eventTabs-dd3-tab2"], ['class'=>'btn btn-primary btn-sm']);
                else
                    echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'), [$this->context->returnRoute, 'id'=>$event->id, "#"=>"tab-gear"], ['class'=>'btn btn-primary btn-sm']); ?>
            <?php if ($type=="event"){ echo Html::a(Yii::t('app', 'Magazyn zewnętrzny'), array_merge(['outer-warehouse/assign'], $_GET), ['class'=>'btn btn-success btn-sm']); } ?>
            </div>
            </div>
            </div>
        </div>
        <div class="col-md-8">
        <?php
            echo $this->render('_toolsAssign', ['warehouse'=>$warehouse]);

        ?>
        </div>
    </div>


        <?php

        echo $this->render('_sets', ['gearSet'=>$gearSets, 'event'=>$event, 'type'=>$type, 'category'=>$category]);

        $gearColumns = [
            [
                'content'=>function($model, $key, $index, $grid) use ($warehouse)
                {
                    $activeModel = $warehouse->activeModel;
                    if ($model->getGearItems()->count() == 0 && $model->no_items==0)
                    {
                        return ''; //po co rozwijać, jak nie ma
                    }
                    if ($model->no_items == 1) {
                        return;
                    }
                    $icon = $activeModel==$model->id ? 'arrow-up' : 'arrow-down';
//                    $icon = $activeModel==$model->id ? 'eye-close' : 'eye-open';
                    $id = $activeModel==$model->id ?  null : $model->id;
                    return Html::a(Html::icon($icon), Url::current(['activeModel'=>$id]), ['class'=>$icon]);

                }
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>false,
                'checkboxOptions' => function ($model, $key, $index, $column) use ($type, $event, $warehouse) {
                    $disabled = true;
                    if ($warehouse->getGearAvailableCount($model) == 0) {
                        $disabled = true;
                    }
                    $display = null;
                    if ($model->no_items == 1) {
                        $display = ['display' => 'none'];
                    }
                    return ['checked' => $model->getIsGearAssigned($event), 'class'=>'checkbox-model', 'disabled' => $disabled, 'style' => $display ];
                }
            ],
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

                        $assignmentForm = new \common\models\form\GearAssignment();
                        $assignmentForm->warehouse = $warehouse;
                        //$item = $model->getNoItemsItem();
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
                'format' => 'html',
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
                        return $gear->getAvailabe($warehouse->from_date, $warehouse->to_date)+$assigned-$serwisNumber . " " . $serwis;
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

                        return ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber)+$assigned . " " . $serwis." ".$need;
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
                'value' => function ($model) use ($warehouse, $user) {
                    $return = "";
                    if ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL]))
                    {
                    $working = $model->getEvents($warehouse->from_date, $warehouse->to_date);
                    $workingNear = $model->getEventsNear($warehouse->from_date, $warehouse->to_date);
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
                    
                },
                'visible'=> ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL])),
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
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'filterModel' => null,
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
            'columns' => $gearColumns,
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
$eventGearConnectedUrl = Url::to(['warehouse/assign-gear-connected', 'id'=>$event->id, 'type'=>$type]);
$eventGearOuterConnectedUrl = Url::to(['outer-warehouse/manage-gear-connected', 'event_id'=>$event->id]);
$checkGearConflictsUrl = Url::to(['warehouse/gear-conflicts', 'event_id'=>$event->id]);
$eventGearConflictsUrl = Url::to(['warehouse/gear-conflicts-modal', 'event_id'=>$event->id]);
$eventGearSimilarUrl = Url::to(['warehouse/gear-similar', 'id'=>$event->id]);
$saveSimilarUrl = Url::to(['warehouse/save-similar', 'id'=>$event->id]);
$saveConflictUrl = Url::to(['warehouse/save-conflict', 'id'=>$event->id]);
$eventGearUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearGroupCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventGroupUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
$eventGearQuantityUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1]);
$eventGearModelUrl = Url::to(['gear/get-gear-as-json']);
$eventGearList = Url::to(['warehouse/get-assigned-gear', 'event_id'=>$event->id, 'type'=>$type]);
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";
$this->registerJs('

        function changeQuantityForm(){
        $(".gear-quantity").unbind("change");
$(".gear-quantity").on("change", function(e){
    
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    var value = $(this).val();
    var oldValue = form.find("#gearassignment-oldquantity").val();
    var gear_id = form.find("#gearassignment-itemid").val();
    $(".gear-quantity").prop("disabled", true);
    $.post("'.$eventGearQuantityUrl.'", data, function(response){
         $(".gear-quantity").prop("disabled", false);
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
                showSimilarModal(data);
        }
        else
        {
            form.find("#gearassignment-oldquantity").val(value);
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
    });
        $(".gear-assignment-form").yiiActiveForm("updateAttribute", "gearassignment-quantity", error);
        
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

$(".select-on-check-all").each(function(){
    var disabled = true;
    $(this).parent().parent().parent().next().find(":checkbox").each(function(){
        if ($(this).prop("disabled") == false) {
            disabled = false;    
        }
    });
    $(this).prop("disabled", disabled);
});


$(".grid-view-items :checkbox").not(".select-on-check-all, .checkbox-group").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventGear(id, add);
    if (!add) {
        $("span.remove_one_model[data-id=\'"+id+"\']").trigger("click");
    }
});
$(".grid-view-items :checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var elements = $(this).closest("table").find("tbody :checkbox").not(".checkbox-group");

    elements.each(function(index,el) {
        var id = $(el).val();
        var add = $(el).prop("checked");
        eventGear(id, add);
        if (!add) {
            $("span.remove_one_model[data-id=\'"+id+"\']").trigger("click");
        }
    });
    
});

$(".gear-groups .checkbox-group").not(".select-on-check-all").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventGroup(id, add);
    if (!add) {
        $("span.remove_one_group[data-id=\'"+id+"\']").trigger("click");
    }
});
$(".gear-groups :checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var elements = $(this).closest("table").find(".checkbox-group");
    elements.each(function(index,el) {
        var id = $(el).val();
        var add = $(el).prop("checked");
        eventGroup(id, add);
        if (!add) {
            $("span.remove_one_group[data-id=\'"+id+"\']").trigger("click");
        }
    });
    
});
function eventGroup(id, add) {
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventGearGroupCheckUrl.'", data, function(response){
    if (response.success)
    {
    $.post("'.$eventGroupUrl.'", data, function(response){
        if (add && response.gear && response.gear_group) {
            addGearRow(response.gear);  
            addGearGroupRow(response.gear_group);

        }
        if (add)
        {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }
            resolveConflict(0,0);
        }
        else{
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
        }
    });
    }else{
        toastr.error("'.Yii::t('app', 'Brak wolnych egzemplarzy').'");
    }
    });
}

function eventGear(id, add) {
    
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventGearCheckUrl.'", data, function(response){
    if (response.success)
    {
        $.post("'.$eventGearUrl.'", data, function(response){
            if(add && response.gear && response.gear_item) {
                
                // jeżeli nie ma w tabeli już ten model
                if ($(".gear-row[data-gearid=\'"+response.gear.id+"\']").length == 0) {
                    addGearRow(response.gear);
                }
                addGearItemRow(response.gear_item);
            }
            if (add)
            {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }
                resolveConflict(0,0);
            }
            else{
                toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
            }
        });        
    }else{
        toastr.error("'.Yii::t('app', 'Brak wolnych egzemplarzy').'");
    }
    });

}

function addGearRow(gear) {
    if (!gear) {
        return;
    }
    
    if ($(".gear-row[data-gearid=\'"+gear.id+"\']").length === 1) {
        return;
    }
    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(gearRow(gear));
        }
    });
}
function addGearItemRow(gear) {
    if (!gear) {
        return;
    }
    
    var modelRow = $(".gear-row[data-gearid=\'"+gear.gear_id+"\']");
    var itemRow = $(".gear-item-row[data-gearitemid=\'"+gear.id+"\']");

    if (modelRow.length === 1) {
        var numberTd =  modelRow.find("td:nth-child(3)");
        numberTd.html((parseInt(numberTd.html())+1));
        if (modelRow.next().hasClass("sub_models")) {
            modelRow.next().find("tbody").append(gearItem(gear));
        }
        else {
            modelRow.after(
                "<tr class=\'sub_models\' style=\'display: none;\'>"+
                    "<td colspan=\'5\'>"+
                        "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                            "<thead><tr><td>'.Yii::t('app', 'Id').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                            "<tbody>"+gearItem(gear)+"</tbody>"+
                    "</td>"+
                "</tr>"
            );
        }
        modelRow.find("td:nth-child(5)").append("<span class=\'numbers-item-gear\' data-id=\'"+gear.id+"\'>"+gear.number+", </span>");
    }
    else {
        //alert("Błąd nr: #000158qad666");
    }
}
function gearItem(gear) {
    return "<tr class=\'gear-item-row\' data-gearitemid=\'"+gear.id+"\'>"+
                "<td>"+gear.id+"</td>"+
                "<td></td>"+
                "<td>"+gear.name+"</td>"+
                "<td><span class=\'checkbox-item-gear\' data-id=\'"+gear.id+"\'>"+gear.number+"</span></td>"+
                "<td><span class=\'remove_one_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+gear.id+"\' data-gearid=\'"+gear.gear_id+"\'></span></td>"+
            "</tr>";
}

function gearRow(gear) {
    if (gear.photo) {
        img = "<img src=\'/uploads/gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
    }

    return  "<tr class=\'gear-row\' data-gearid=\'"+gear.id+"\' >" +
                "<td>"+gear.id+"<span class=\'row-warehouse-out glyphicon glyphicon-arrow-down\' style=\'cursor:pointer;\'></span></td>" +
                "<td>"+img+"</td>"+
                "<td>0</td>"+
                "<td>"+gear.name+"</td>"+
                "<td></td>"+
                "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear.id+"\'></span></td>"+
            "</tr>";
}

function addGearGroupRow(group) {
    if (!group) {
        return;
    }
    
    var modelRow = $(".gear-row[data-gearid=\'"+group.items[0].gear_id+"\']");
    var groupRow = $(".gear-item-case-row[data-groupid=\'"+group.id+"\']");
    var numbers = "[";
    
    var numer_list = null;
    var ids = [];
    for (var i = 0; i < group.items.length; i++) {                        
        numbers += group.items[i].number + ", ";
        ids[i] = group.items[i].number;
    }
    numbers += "], ";
    var in_order = true;
    for (var i = Math.min.apply(null, ids); i < Math.max.apply(null, ids); i++) {
        if ($.inArray(i, ids) === -1) {
            in_order = false;
        }
    }
    if (in_order) {
        numbers = "[" + Math.min.apply(null, ids) + "-" + Math.max.apply(null, ids) + "]";
    }

    if (modelRow.length === 1) {
        if (modelRow.next().hasClass("sub_models")) {
            modelRow.next().find("tbody").append(gearGroupRow(group));
        }
        else {
            modelRow.after(
                "<tr class=\'sub_models\' style=\'display: none;\'>"+
                    "<td colspan=\'5\'>"+
                        "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                            "<thead><tr><td>'.Yii::t('app', 'Id').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                            "<tbody>"+gearGroupRow(group)+"</tbody>"+
                    "</td>"+
                "</tr>"
            );
        }
        var numberTd =  modelRow.find("td:nth-child(3)");
        numberTd.html((parseInt(numberTd.html())+group.items.length));
        modelRow.find("td:nth-child(5)").append("<span class=\'numbers-item-group\' data-id=\'"+group.id+"\'>"+numbers+"</span>");
    }
    else {
        //alert("'.Yii::t('app', 'Błąd nr').': #00017do%domu623478");
    }
}
function gearGroupRow(group) {
    if (!group) {
        return;
    }
    
    var itemNames = "";
    var itemCodes = "";
    for (var i = 0; i < group.items.length; i++) {
        itemNames += group.items[i].name + "<br>";
        itemCodes += "numer: " + group.items[i].number + "<br>";
    }

    return "<tr class=\'checkbox-group  gear-item-case-row\' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\'>"+
                "<td>"+group.id+"</td>"+
                "<td><img src=\'/admin/../img/case.jpg\' alt=\'\' style=\'width:100px;\' ></td>"+
                "<td>"+itemNames+"</td>"+
                "<td>"+itemCodes+"</td>"+
                "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\'></span></td>"+
            "</tr>";
}

function eventModel(id, add) {
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventModelUrl.'", data, function(response){
        if (add) {
            if (response.gear) {
                addGearRow(response.gear);  
            }
            if (response.gear_items) {
                for (var i = 0; i < response.gear_items.length; i++) {
                    addGearItemRow(response.gear_items[i]);
                }
            }
            if (response.gear_groups) {
                for (var i = 0; i < response.gear_groups.length; i++) {
                    addGearGroupRow(response.gear_groups[i]);
                }
            }
        }
        if (add)
        {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
            resolveConflict(0,0);
        }
        else{
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
        }
    });
}

$(":checkbox.checkbox-model").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventModel(id, add);
    if (add == false) {
        var gear_row = $(".gear-row[data-gearid=\'"+id+"\']");
        if (gear_row.next().hasClass("sub_models")) {
            gear_row.next().remove();
        }
        gear_row.remove();
    }
    
    var tr = $(this).closest("tr").next("tr");
    if (tr.hasClass("gear-details")) {
        tr.find(":checkbox").each(function() {
            if ($(this).prop("disabled") === false) {
                $(this).prop("checked", add);
            }
        });
    }
    
    return false;
});

$(".gear-quantity").on("change", function(e){
    
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    var value = $(this).val();
    var oldValue = form.find("#gearassignment-oldquantity").val();
    var gear_id = form.find("#gearassignment-itemid").val();
    $(".gear-quantity").prop("disabled", true);
    $.post("'.$eventGearQuantityUrl.'", data, function(response){
         $(".gear-quantity").prop("disabled", false);
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
                showSimilarModal(data);
        }
        else
        {
            form.find("#gearassignment-oldquantity").val(value);
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
        
    });
    
   
   
   $.get("'.$eventGearModelUrl.'?id="+gear_id, null, function(gear){
        $.when(addGearRow(gear)).then(function(){
            var gear_row = $(".gear-row[data-gearid=\'"+gear_id+"\']");
            $(gear_row.children()[0]).html(gear_id);
            $(gear_row.children()[2]).html(value);
        });
   });
   
    
   return false;
});

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
        function showConnectedModal(gears, outergears){
        var modal = $("#connected_modal");
        modal.find(".modalContent").empty();
        var content = "<table class='table'><thead><tr><th>#</th><th>Nazwa</th><th>Liczba sztuk</th></tr></thead><tbody>";
        for (var i=0; i<gears.length; i++)
        {
            if (gears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-connectedcheckbox\'  data-gearid=\'"+gears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+gears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+gears[i].count+"'/></td></tr>";
        }
        for (var i=0; i<outergears.length; i++)
        {
            if (outergears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-outerconnectedcheckbox\'  data-gearid=\'"+outergears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+outergears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+outergears[i].count+"'/></td></tr>";
        }
        content += "</tbody></table>";
        content += '<div class="row"><div class="pull-right"><a class="btn btn-primary add-connected-button" href="#">Dodaj</a> ';
        content += '<a class="btn btn-default close-connected-button" href="#">Anuluj</a></div></div>';
        modal.find(".modalContent").append(content);        
        modal.modal("show");
        $(".add-connected-button").click(function(){ saveConnected();})
        $(".close-connected-button").click(function(){  $("#connected_modal").modal("hide");})
        }

        function showSimilarModal(data){
            <?php if ($type=='event'){ ?>
            var modal = $("#similar_modal");
            modal.find(".modalContent").empty();
            $.post("<?=$eventGearSimilarUrl?>", data, function(response){
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
            $.post('<?=$saveSimilarUrl?>', $("#similarForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                                $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignment-quantity").first().val(response.responses[i].total);
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
            $.post('<?=$saveConflictUrl?>', $("#conflictForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                            $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignment-quantity").first().val(response.responses[i].total);
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
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity
                }
                $.post("<?=$eventGearConnectedUrl?>", data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                                    $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignment-quantity").first().val(response.responses[i].total);
                                    
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
              location.href = "<?=Url::to(['warehouse/conflict-partial', 'id'=>$conflict]);?>&old="+oldValue+"&quantity="+newValue;
              break;       
          }
        });
        <?php } ?>
    }
</script>
