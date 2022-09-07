<?php
/* @var $this \yii\web\View */
/* @var $event \common\models\Event */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\models\GearService;
use common\models\Rent;
use common\models\RentGearItem;
use yii\bootstrap\Modal;
use common\components\grid\GridView;
use common\models\EventGearItem;
use common\models\GearItem;
use common\models\Event;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use yii\widgets\Pjax;
\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);

$this->title = Yii::t('app', 'Przypisz sprzęt').' - ' . $event->name;
?>

<?= $this->render('_summaryTable', ['event'=>$event->id, 'type' => $type]) ?>

<div class="menu-pils">
    <?= $this->render('_categoryMenu'); ?>
</div>
    <?php
echo $this->render('_toolsAssign', ['warehouse'=>$warehouse]);

?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
        <div class="ibox float-e-margins">
            <?php echo Html::a(Html::icon('arrow-left').' Zapisz i wróć', [$this->context->returnRoute, 'id'=>$event->id, "#"=>"tab-gear"], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Yii::t('app', 'Magazyn zewnętrzny'), array_merge(['outer-warehouse/assign'], $_GET), ['class'=>'btn btn-success']); ?>
            </div>
        </div>

    </div>
<!--    --><?php //Pjax::begin([
//        'id'=>'warehouse-pjax-container',
//        'linkSelector' => 'a:not(.linksWithTarget)',
//    ]); ?>
    <div class="gear gears row">
        <div class="ibox float-e-margins">
                <div class="ibox-title newsystem-bg">
                    <h4><?php echo $title; ?></h4>
                </div>
        <div class="ibox-content">
        <?php
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
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['gear/update', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label' => '',
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
                            'style' => 'width: 68px;',
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Zapisz'), [
                            'class'=>'btn btn-default gear-quantity',
                            'disabled'=> $isAvailable ? false : true,
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();

                        $content .= $this->blocks['form'];
                    //}
                    return $content;
                }
            ],
            [
                'attribute'=>'quantity',
                'value'=>function($gear, $key, $index, $column)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items==true)
                    {
                        return $gear->quantity;
                    }
                    else
                    {
                        return $gear->getGearItems()->andWhere(['active' => 1])->count();
                    }
                }
            ],
            [
                'attribute'=>'available',
                'format' => 'html',
                'value'=>function($gear, $key, $index, $column) use ($warehouse)
                {
                    if ($gear->no_items)
                    {
                        return $gear->getAvailabe($warehouse->from_date, $warehouse->to_date);
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }

                        $serwis = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }

                        return ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber) . " " . $serwis;
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Zarezerwowany'),
                'format' => 'raw',
                'value' => function ($model) use ($event) {
                    $start = new DateTime($event->getTimeStart());
                    $end = new DateTime($event->getTimeEnd());
                    $negativeInterval = new DateInterval("P1D");
                    $negativeInterval->invert = 1;
                    $start->add($negativeInterval);
                    $end->add(new DateInterval("P1D"));

                    $gearItems = GearItem::find()->where(['gear_id'=>$model->id])->all();

                    $working = [];
                    $working_rents = [];
                    foreach ($gearItems as $gearItem) {
                        $working1 = EventGearItem::find()
                            ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                            ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->all();
                        $working2 = EventGearItem::find()
                            ->where(['<=', 'start_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['>=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->all();
                        $working = array_merge($working, $working1);
                        $working = array_merge($working, $working2);

                        $rents1 = RentGearItem::find()
                            ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                            ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->all();
                        $rents2 = RentGearItem::find()
                            ->where(['<=', 'start_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['>=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['.gear_item_id' => $gearItem->id])
                            ->all();
                        $working_rents = array_merge($working_rents, $rents1);
                        $working_rents = array_merge($working_rents, $rents2);
                    }

                    $event_number_gears = [];
                    /** @var \common\models\EventGearItem $eventGear */
                    foreach ($working as $eventGear) {
                        if (isset($event_number_gears[$eventGear->event_id])) {
                            $event_number_gears[$eventGear->event_id]++;
                        }
                        else {
                            $event_number_gears[$eventGear->event_id] = 1;
                        }
                    }

                    $result = "";
                    $showed = [];
                    $number = [];
                    foreach ($working as $eventGear) {
                        if (!isset($number[substr($eventGear->start_time, 0, 10)][substr($eventGear->end_time, 0, 10)])) {
                            $number[substr($eventGear->start_time, 0, 10)][substr($eventGear->end_time, 0, 10)] = 0;
                        }
                        $number_index = $event_number_gears[$eventGear->event_id];
                        if (!in_array([$eventGear->start_time, $eventGear->end_time], $showed)) {
                            $showed[] = [$eventGear->start_time, $eventGear->end_time];
                            $event_model = Event::find()->where(['id'=>$eventGear->event_id])->one();
                            if ($eventGear->gearItem->name == '_ILOSC_SZTUK_') {
                                $number_index = $eventGear->quantity;
                            }
                            $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $event_model->name . " (".$number_index.")<br>", ['event/view',
                                'id' => $eventGear->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);
                        }
                    }

                    $number_of_gears = [];
                    /** @var \common\models\RentGearItem $rentGearItem */
                    foreach ($working_rents as $rentGearItem) {
                        if (is_numeric($rentGearItem->quantity) && $rentGearItem->quantity > 0) {
                            $number_of_gears[$rentGearItem->rent_id] = $rentGearItem->quantity;
                            continue;
                        }
                        if (isset($number_of_gears[$rentGearItem->rent_id])) {
                            $number_of_gears[$rentGearItem->rent_id]++;
                        }
                        else {
                            $number_of_gears[$rentGearItem->rent_id] = 1;
                        }
                    }

                    $showed = [];
                    /** @var $rent common\models\Rent */
                    foreach ($working_rents as $rentGearItem) {
                       if (!in_array($rentGearItem->rent_id, $showed)) {
                            $showed[] = $rentGearItem->rent_id;
                            $result .= Html::a(substr($rentGearItem->start_time, 0, 10) . " - " . substr($rentGearItem->end_time, 0, 10) . " " . $rentGearItem->rent->name . " (".$number_of_gears[$rentGearItem->rent_id].")<br>", ['rent/view',
                                'id' => $rentGearItem->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);
                        }
                    }

                    return $result;
                }
            ],
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
            'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $warehouse, $assignedItems, $type, $event)
            {
                $activeModel = $warehouse->activeModel;
                $content = '';
                $rowOptions =  [
                    'class'=>'gear-details',
                ];
                if ($model->id != $activeModel)
                {
                    return false;
                }
                if ($model->no_items == 0)
                {
                    $content = GridView::widget([
                        'layout'=>'{items}',
                        'dataProvider'=>$warehouse->getGearItemDataProvider(),
                        'options'=>[
                            'class'=>'grid-view grid-view-items',
                        ],
                        'rowOptions'=> function ($model, $key, $index, $grid) use ($event)
                        {
                            return [
                                'class'=> $model->isAvailable($event) ? '' : 'danger',
                            ];
                        },
                        'filterModel' => null,
                        'columns' => [
                            [
                                'headerOptions' => [
//                                    'class'=>'checkbox-item select-on-check-all',
                                ],
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) use ($assignedItems, $type, $event) {
                                    /* @var $model \common\models\GearItem */
                                    return [
                                        'checked' => key_exists($model->id, $assignedItems),
                                        'class'=>'checkbox-item',
                                        'disabled'=>$model->isAvailable($event) ? false : true,
                                        'data' => ['gearid'=>$model->gear_id],
                                    ];
                                }
                            ],
                            [
                                'header' => Yii::t('app', 'Nazwa'),
                                'format' => 'html',
                                'value' => function ($gear) {
                                    $service = GearService::getCurrentModel($gear->id);
                                    if ($service != null) {
                                        return Html::a($gear->name, ['view', 'id'=>$gear->id]) . " " . Html::a($gear->getStatusLabel(), ['/gear-service/view', 'id'=>$service->id], ['class'=>'label label-danger']);
                                    }
                                    if ($gear->status == GearItem::STATUS_SERVICE) {
                                        return $gear->name . " " . Html::tag('span', $gear->getStatusLabel(), ['class'=>'label label-danger']);
                                    }
                                    
                                    return $gear->name;
                                }
                            ],
                            'number:text:'.Yii::t('app', 'Nr'),
                            [
                                'label' => Yii::t('app', 'Zarezerwowany'),
                                'format' => 'raw',
                                'value' => function ($model) use ($event) {
                                    $start = new DateTime($event->getTimeStart());
                                    $end = new DateTime($event->getTimeEnd());
                                    $negativeInterval = new DateInterval("P1D");
                                    $negativeInterval->invert = 1;
                                    $start->add($negativeInterval);
                                    $end->add(new DateInterval("P1D"));
                                    $gearItem = $model;

                                    $working1 = EventGearItem::find()
                                        ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                                        ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['gear_item_id' => $gearItem->id])
                                        ->all();
                                    $working2 = EventGearItem::find()
                                        ->where(['<', 'start_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['>=', 'end_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['gear_item_id' => $gearItem->id])
                                        ->all();
                                    $working = array_merge($working1, $working2);
                                    $result = "";
                                    foreach ($working as $eventGear) {
                                        $showed[] = [$eventGear->start_time, $eventGear->end_time];
                                        $display_value = $eventGear->start_time . " - " . $eventGear->end_time;
                                        $result .= Editable::widget([
                                            'formOptions' => [
                                                'action'=>['event/update-working-time-event-gear-item', 'eventId'=>$eventGear->event_id, 'gearId' => $gearItem->gear_id, 'itemId' => $gearItem->id],
                                            ],
                                            'asPopover' => true,
                                            'placement' => PopoverX::ALIGN_RIGHT,
                                            'inputType' => Editable::INPUT_DATE_RANGE,
                                            'header' => Yii::t('app', 'Czas pracy'),
                                            'size' => PopoverX::SIZE_LARGE,
                                            'model' => $eventGear,
                                            'attribute' => 'dateRange',
                                            'displayValue' => $display_value,
                                            'submitButton'=>[
                                                'icon' => Html::icon('ok'),
                                                'class'=>'btn btn-sm btn-primary change-working-time-period',
                                                'data' => [
                                                    'eventid' => $eventGear->event_id,
                                                    'gearid' => $gearItem->gear_id,
                                                    'itemid' => $gearItem->id,
                                                ],
                                            ],
                                            'containerOptions' => ['style'=>'display: inline-block; white-space: nowrap;', 'class' => 'container-working-time'],
                                            'options'=> [

                                                'id'=>'edit-'.$gearItem->id.'-'.$eventGear->event_id,
                                                'options'=>[
                                                    'style'=>'width: 100%',
                                                    'id'=>'picker-'.$gearItem->id.'-'.$eventGear->event_id,
                                                    'class'=>'form-controll'
                                                ],
                                                'convertFormat'=>true,
                                                'startAttribute' => 'start_time',
                                                'endAttribute' => 'end_time',
                                                'pluginOptions'=>[
                                                    'timePicker'=>true,
                                                    'timePickerIncrement'=>5,
                                                    'timePicker24Hour' => true,
                                                    'locale'=>['format' => 'Y-m-d H:i:s'],
                                                ],
                                            ],
                                            'pluginEvents' => [
                                                "editableBeforeSubmit"=>"function(event, jqXHR) { 
                                                event.preventDefault(); 
                                                $('.kv-editable-loading').hide();
                                                var button = $('.kv-editable-popover').find('button.change-working-time-period.kv-editable-submit:visible');
                                                var inputText = $('.kv-editable-content').find('input.kv-editable-input:visible').val();
                                                var dateString = inputText.split(' ');
                                                
                                                var postData = {
                                                    EventGearItem: {
                                                        start_time: dateString[0] + ' ' + dateString[1],
                                                        end_time: dateString[3] + ' ' + dateString[4],
                                                    },
                                                };
                                                
                                                $.ajax({
                                                    type: 'POST',
                                                    data: postData,
                                                    url: '".Url::to(['event/update-working-time-event-gear-item'])."?eventId=' +button.data(\"eventid\") + '&gearId=' + button.data(\"gearid\")+ '&itemId=' + button.data(\"itemid\"),
                                                    success: function(response) {
                                                        $('#edit-' + button.data('itemid') + '-' + button.data('eventid') + '-targ').html(inputText);
                                                        $('.close').trigger('click');
                                                        location.reload();
                                                    }
                                                });
                                                
                                            }",
                                            ],
                                        ]);
                                        $gearEvent = Event::find()->where(['id' => $eventGear->event_id])->one();
                                        $result .= Html::a("<div>".$gearEvent->name."</div>",
                                            [
                                                'event/view',
                                                'id' => $eventGear->event_id, "#" => "tab-gear"
                                            ],
                                            [
                                                'target' => '_blank',
                                                'class' => 'linksWithTarget',
                                                'data-pjax' => 0,
                                                'style' => 'color:red;'
                                        ]);
                                    }
                                    $rents1 = Rent::find()
                                        ->where(['>', 'rent.end_time', $start->format("Y-m-d H:i:s")])
                                        ->andWhere(['<=', 'rent.end_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['rent_gear_item.gear_item_id' => $gearItem->id])
                                        ->innerJoin('rent_gear_item', 'rent.id = rent_gear_item.rent_id')->all();
                                    $rents2 = Rent::find()
                                        ->where(['<=', 'rent.start_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['>=', 'rent.end_time', $end->format("Y-m-d H:i:s")])
                                        ->andWhere(['rent_gear_item.gear_item_id' => $gearItem->id])
                                        ->innerJoin('rent_gear_item', 'rent.id = rent_gear_item.rent_id')->all();
                                    $working_rents = array_merge($rents1, $rents2);

                                    foreach ($working_rents as $rent) {
                                        $result .= Html::a(substr($rent->start_time, 0, 10) . " - " . substr($rent->end_time, 0, 10) . " " . $rent->name . "<br>", ['rent/view',
                                            'id' => $rent->id], ['target' => '_blank',
                                            'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);
                                    }
                                    return $result;
                                }
                            ],
                        [
                            'header' => Yii::t('app', 'Sprawdzony'),
                            'format' => 'html',
                           'value' => function ($gear) {
                                $date = "";
                                if ($gear->test_date)
                                    $date = " (".date("d.m.Y", strtotime($gear->test_date)).")";
                                return $gear->tester.$date;
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Godziny lamp'),
                            'format' => 'html',
                            'value' => function ($gear) {
                                return $gear->lamp_hours;
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Uwagi'),
                            'format' => 'html',
                            'value' => function ($gear) {
                                return $gear->info;
                            },

                        ],
                        ],
                    ]);
                    $content = $this->render('_groupassign', [
                            'checkbox'=>true,
                            'warehouse'=>$warehouse,
                            'gearColumns'=>$gearColumns,
                            'assignedItems'=>$assignedItems,
                            'type'=>$type,
                            'event'=>$event,
                        ]).$content;
                    $content = Html::tag('div', $content, ['class'=>'wrapper']);
                }

                $content = Html::tag('div', $content, ['class'=>'wrapper']);
                return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]), $rowOptions);
            },

            'columns' => $gearColumns,
        ]); ?>
            </div>
        </div>
    </div>
    </div>

</div>

<?php
$eventGearUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGroupUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
$eventGearQuantityUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1]);
$eventGearModelUrl = Url::to(['gear/get-gear-as-json']);
$this->registerJs('

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
    $.post("'.$eventGroupUrl.'", data, function(response){
        if (add && response.gear && response.gear_group) {
            addGearRow(response.gear);  
            addGearGroupRow(response.gear_group);

        }
        if (add)
        {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
        }
        else{
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
        }
    });
}

function eventGear(id, add) {
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
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
        }
        else{
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
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
        alert("Błąd nr: #000158qad666");
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
        alert("'.Yii::t('app', 'Błąd nr').': #00017do%domu623478");
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

$(".gear-quantity").on("click", function(e){
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    $.post("'.$eventGearQuantityUrl.'", data, function(response){
         
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
        }
        else
        {
            var container = $("[data-pjax-container]");
//            $.pjax.reload("#" + container.attr("id"), {
//                push: false,
//                replace: true,
//            });
        }
        $(".gear-assignment-form").yiiActiveForm("updateAttribute", "gearassignment-quantity", error);
        
    });
    
   var value = $(this).prev().find("input").val();
   var gear_id = $(this).data("gearid");
   
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

');
$this->registerCss('
    .display_none {display: none;}
');
?>

