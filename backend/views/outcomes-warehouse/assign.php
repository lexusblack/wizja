<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\models\EventGearItem;
use common\models\Event;
use common\models\Rent;
use common\models\GearItem;
use common\models\OutcomesGearOur;
use common\models\RentGearItem;
use kartik\editable\Editable;
use common\components\grid\GridView;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;


?>

<div class="menu-pils">
    <?php /* $this->render('../warehouse/_categoryMenu'); */ ?>
</div>
<?php

$event = \common\models\Event::find()->where(['id'=>$event])->one();
$rent = \common\models\Rent::find()->where(['id'=>$rent])->one();
?>
<div class="warehouse-container">

    <div class="gear gears">
        <?php
        /*
        $gearColumns = [

            // strzalka do rozwijania jeżeli jest więcej niż 1 egzemplarz sprzętu
            [
                'content'=>function($model, $key, $index, $grid) use ($warehouse)
                {
                    $activeModel = $warehouse->activeModel;
                    if ($model->getGearItems()->count()==1)
                    {
                        if ($model->no_items = 1) {
                            $value = 0;
                            if (isset($_COOKIE['checkbox-item-gear'][$model->gearItems[0]->id])) {
                                $value = $_COOKIE['checkbox-item-gear'][$model->gearItems[0]->id];
                            }
                            return Html::input('number', 'number', $value, ['class' => 'number_no_items', 'min' => 0, 'max' => $model->quantity]);
                        }
                        return Html::icon('ban-circle');
                    }
                    else
                    {
                        if ($model->getGearItems()->count() == 0)
                        {
                            return ''; //po co rozwijać, jak nie ma
                        }
                        $icon = $activeModel==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeModel==$model->id ?  null : $model->id;
                        return Html::a(Html::icon($icon), Url::current(['activeModel'=>$id]), ['class'=>$icon." category-menu-link"]);
                    }
                },
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>false,
                'checkboxOptions' => function ($model, $key, $index, $column) use ($warehouse) {



                    $toReturn = [
                        'class'=>'checkbox-model',
                        'disabled'=>false,
                    ];
                    if ($model->getGearItems()->count() == 1) {
                        $toReturn['value'] = $model->gearItems[0]->id;
                        $toReturn['disabled'] = false;
                        $toReturn['class'] = 'checkbox-item-id';

                        if ($model->no_items == 1) {
                            $toReturn['class'] .= " itemnoitems";
                        }

                        if (!$model->gearItems[0]->isAvailableForOutcome()) {
                            $toReturn['disabled'] = true;
                        }

                        if (isset($_COOKIE['checkbox-item-gear'][$model->gearItems[0]->id])) {
                            $toReturn['checked'] = true;
                        }

                    }
                    if ($model->numberOfAvailable() <= 0) {
                        $toReturn['disabled'] = true;
                    }
                    return $toReturn;
                },
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
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Zarezerwowany'),
                'format' => 'raw',
                'value' => function ($model) use ($event, $rent) {
                    if (!$event && !$rent) {
                        return null;
                    }
                    if ($event) {
                        $start = new DateTime($event->getTimeStart());
                        $end = new DateTime($event->getTimeEnd());
                    }
                    if ($rent) {
                        $start = new DateTime($rent->getTimeStart());
                        $end = new DateTime($rent->getTimeEnd());
                    }
                    $negativeInterval = new DateInterval("P1D");
                    $negativeInterval->invert = 1;
                    $start->add($negativeInterval);
                    $end->add(new DateInterval("P1D"));
                    $gearItems = GearItem::find()->where(['gear_id'=>$model->id])->all();

                    $working = [];
                    $working3 = [];
                    foreach ($gearItems as $gearItem) {
                        $event_id = null;
                        $rent_id = null;
                        if ($event) {
                            $event_id = $event->id;
                        }
                        if ($rent) {
                            $rent_id = $rent->id;
                        }
                        $working1 = EventGearItem::find()
                            ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                            ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->andWhere(['<>', 'event_id', $event_id])
                            ->all();
                        $working2 = EventGearItem::find()
                            ->where(['<', 'start_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['>=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->andWhere(['<>', 'event_id', $event_id])
                            ->all();
                        $working3 = RentGearItem::find()
                            ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                            ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                            ->andWhere(['gear_item_id' => $gearItem->id])
                            ->andWhere(['<>', 'rent_id', $rent_id])
                            ->all();
                        $working = array_merge($working, $working1);
                        $working = array_merge($working, $working2);
                        //$working = array_merge($working, $working3);
                    }
                    $result = "";
                    $showed = [];
                    foreach ($working as $eventGear) {
                        if (!in_array($eventGear->event_id, $showed)) {
                            $number = 0; {
                                foreach ($working as $item) {
                                    if ($item->event_id == $eventGear->event_id) {
                                        $number++;
                                    }
                                }
                            }
                            $showed[] = $eventGear->event_id;
                            $gearEvent = Event::find()->where(['id' => $eventGear->event_id])->one();
                            $result .=  Html::a( "<div style='white-space: nowrap;'>" .
                                substr($eventGear->start_time, 0, 10) . " - " .
                                substr($eventGear->end_time, 0, 10) ." ".
                                $gearEvent->name . " (".$number.")</div>",
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
                    }
                    $showed = [];
                    foreach ($working3 as $eventGear) {
                        if (!in_array($eventGear->rent_id, $showed)) {
                            $number = 0; {
                                foreach ($working3 as $item) {
                                    if ($item->rent_id == $eventGear->rent_id) {
                                        $number++;
                                    }
                                }
                            }
                            $showed[] = $eventGear->rent_id;
                            $gearEvent = Rent::find()->where(['id' => $eventGear->rent_id])->one();
                            $result .=  Html::a( "<div style='white-space: nowrap;'>" .
                                substr($eventGear->start_time, 0, 10) . " - " .
                                substr($eventGear->end_time, 0, 10) ." ".
                                $gearEvent->name . " (".$number.")</div>",
                                [
                                    'event/view',
                                    'id' => $eventGear->rent_id, "#" => "tab-gear"
                                ],
                                [
                                    'target' => '_blank',
                                    'class' => 'linksWithTarget',
                                    'data-pjax' => 0,
                                    'style' => 'color:red;'
                                ]);
                        }
                    }
                    return $result;
                }
            ],
            [
                'attribute'=>'quantity',
                'value'=>function($gear, $key, $index, $column)
                {
                    if ($gear->no_items==true)
                    {
                        return $gear->quantity;
                    }
                    else
                    {
                        return $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                }
            ],
            [
                'label' => Yii::t('app', 'Sztuk w magazynie'),
                'value' => function ($model) {
                    return $model->numberOfAvailable();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',

                'urlCreator'=>function ($action, $model, $key, $index) {
                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[0] = 'gear/' . $action;

                    return Url::toRoute($params);
                }
            ],
        ]; */
        ?>

        <div class="panel_mid_blocks">
            <div class="panel_block">

        <?php /*
        echo GridView::widget([
            'dataProvider' => $warehouse->getGearDataProvider(),
            'filterModel' => null,
            'layout'=>'{items}',
            'rowOptions' => function ($model) {
                return ['id' => 'row-' . $model->id];
            },
            'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $warehouse, $event, $rent)
            {
                $content = '';

                if ($model->id != $warehouse->activeModel)
                {
                    return false;
                }
                if ($model->getGearItems()->count() == 0)
                {
                    return false;
                }
                $content .= GridView::widget([
                    'dataProvider' => $warehouse->getGearItemDataProvider(),
                    'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
                    'layout' => '{items}',
                    'options'=>[
                        'class'=>'grid-view grid-view-items',
                    ],
                    'rowOptions' => function ($model, $key, $index, $grid)
                    {
                        $options = [];
                        if ($model->group_id != null) {
                            $options['class'] = 'warning';
                        }
                        if ($model->status == 10) {
                            $options['style'] = ['display' => 'none'];
                        }

                        return $options;

                    },
                    'filterModel' => null,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'headerOptions' => ['class' => 'header-checkbox'],
                            'checkboxOptions' => function($model) use ($event) {
                                $checked = false;
                                $disabled = false;
                                if (isset($_COOKIE['checkbox-item-gear'][$model->id])) {
                                    $checked = true;
                                }
                                if (!$model->isAvailableForOutcome()) {
                                    $disabled = true;
                                }

                                return [
                                    'class'=>'checkbox-item-id ',
                                    'disabled' => $disabled,
                                    'checked'=> $checked,
                                ];
                            },

                        ],
                        'id',
                        'name',
                        [
                            'label' => Yii::t('app', 'Zarezerwowany'),
                            'format' => 'raw',
                            'value' => function ($model) use ($event, $rent) {
                                if (!$event && !$rent) {
                                    return null;
                                }
                                if (!$model->isAvailableForOutcome()) {
                                    if ($model->status == 10) {
                                        return Yii::t('app', 'Sprzęt w serwisie');
                                    }
                                    if ($model->active == 0) {
                                        return Yii::t('app', 'Sprzęt usunięty');
                                    }
                                    $outcomeGroup = OutcomesGearOur::find()->where(['gear_id' => $model->id])->orderBy(['id' => SORT_ASC])->one();

                                    $outcome = $outcomeGroup->outcome;
                                    $outcomeForEvent = $outcome->getOutcomesForEvents()->one();
                                    $outcomeForRent = $outcome->getOutcomesForRents()->one();
                                    if ($outcomeForEvent) {
                                        $event = $outcomeForEvent->event;
                                        $event = Html::a($event->name, Url::toRoute(['event/view', 'id' => $event->id]));
                                    }
                                    if ($outcomeForRent) {
                                        $event = $outcomeForRent->rent;
                                        $event = Html::a($event->name, Url::toRoute(['rent/view', 'id' => $event->id]));
                                    }
                                    return Yii::t('app', "Wydany do eventu").": " . $event;
                                }

                                if ($event) {
                                    $start = new DateTime($event->getTimeStart());
                                    $end = new DateTime($event->getTimeEnd());
                                }
                                if ($rent) {
                                    $start = new DateTime($rent->getTimeStart());
                                    $end = new DateTime($rent->getTimeEnd());
                                };
                                $negativeInterval = new DateInterval("P1D");
                                $negativeInterval->invert = 1;
                                $start->add($negativeInterval);
                                $end->add(new DateInterval("P1D"));
                                $gearItem = $model;

                                $working1 = EventGearItem::find()
                                    ->where(['>', 'end_time', $start->format("Y-m-d H:i:s")])
                                    ->andWhere(['<=', 'end_time', $end->format("Y-m-d H:i:s")])
                                    ->andWhere(['gear_item_id' => $gearItem->id])
                                    ->andWhere(['<>', 'event_id', $event->id])
                                    ->all();
                                $working2 = EventGearItem::find()
                                    ->where(['<', 'start_time', $end->format("Y-m-d H:i:s")])
                                    ->andWhere(['>=', 'end_time', $end->format("Y-m-d H:i:s")])
                                    ->andWhere(['gear_item_id' => $gearItem->id])
                                    ->andWhere(['<>', 'event_id', $event->id])
                                    ->all();
                                $working = array_merge($working1, $working2);
                                $event_start = $start;
                                $event_end = $end;
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
                                                var button = $('.kv-editable-popover').find('button.change-working-time-period.kv-editable-submit');
                                                var inputText = $('.kv-editable-content').find('input.kv-editable-input').val();
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
                                return $result;
                            }
                        ],
                        'number:text:'.Yii::t('app', 'Nr'),
                        [
                            'header' => Yii::t('app', 'Numer QR/Bar'),
                            'value'=>function($model) {
                                return $model->getBarCodeValue();
                            },
                        ],
                        'code:text:'.Yii::t('app', 'Kod'),
                        'serial:text:'.Yii::t('app', 'Nr seryjny'),
                        [
                            'attribute' => 'location',
                            'label' => Yii::t('app', 'Miejsce w<br/>magazynie'),
                            'encodeLabel'=>false,
                        ],
                        'tester',
                        'test_status',
                        [
                            'attribute' => 'lamp_hours',
                            'label' => Yii::t('app', 'Akutalne<br/>godziny lamp'),
                            'encodeLabel'=>false,
                        ],
                        'info:ntext',
                        [
                            'header'=>Yii::t('app', 'Najbliższe<br/>działania'),
                        ],
                        [
                            'attribute' => 'purchase_price',
                            'label' => Yii::t('app', 'Cena<br/>zakupu'),
                            'encodeLabel'=>false,
                        ],
                        [
                            'attribute' => 'refund_amount',
                            'label' => Yii::t('app', 'Kwota<br/>zwrotu'),
                            'encodeLabel'=>false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'urlCreator' =>  function($action, $model, $key, $index)
                            {
                                $params = is_array($key) ? $key : ['id' => (string) $key];
                                $params[0] = 'gear-item/' . $action;

                                return Url::toRoute($params);
                            },
                            'template' => '{history} {view} {update} {delete} {service}',
                            'buttons' => [
                                'history' => function ($url, $model, $key) {
                                    return Html::a(Html::icon('list'), $url);
                                },
                                'service' => function ($url, $model, $key)
                                {
                                    return Html::a(\kartik\helpers\Html::icon('wrench'), $url);
                                }
                            ],
                        ],
                    ],
                ]);
                $content = $this->render('_group', ['checkbox'=>false, 'warehouse'=>$warehouse, 'gearColumns'=>$gearColumns, 'event'=>$event, 'rent'=>$rent]).$content;
                $content = Html::tag('div', $content, ['class'=>'wrapper']);
                return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]), ['class'=>'gear-details']);
            },
            'columns' => $gearColumns,
        ]); */
        ?>

    </div>

        </div>
    </div>

</div>

<?php

if (isset($_GET['activeModel']) && is_numeric($_GET['activeModel'])) {
    $this->registerJs('
        $("html, body").animate({
            scrollTop: $("#row-'.$_GET['activeModel'].'").offset().top - 54
        }, 2000);
    ');
}

$this->registerJs('


$(":checkbox.checkbox-model").click(function(e){
    var add = $(this).prop("checked");
    
    var tr = $(this).closest("tr").next("tr");
    if (tr.hasClass("gear-details")) {
        tr.find(":checkbox").each(function(){
            if ($(this).prop("disabled") == false) {
                $(this).prop("checked", add);
            }
        });
    }
    var modelId = $(this).val();
    
    $.ajax({
        type: "POST",
        url: "'.Url::to(['outcomes-warehouse/get-gear-list']).'?model_id=" + modelId,
        success: function(resp) {
            var gear_items = resp[0];
            var groups = resp[1];
            
            for (var i = 0; i < gear_items.length; i++) {
                if (add) {
                    addRowGearItem(gear_items[i]);
                }
                else {
                    removeRowGearItem(gear_items[i]);
                }
            }
            for (var i = 0; i < groups.length; i++) {
                 if (add) {
                    addGearGroup(groups[i]);
                }
                else {
                    removeGearGroup(groups[i]);
                }
            }
        }
    });
    
});

$(".gear-sort a").on("click", function(e) {
    e.preventDefault();
    var el = $(this);
    var row = el.closest("tr");

    if (el.hasClass("sort-up"))
    {
        var el2 = row.prev("tr");
        if (el2)
        {
            row.insertBefore( el2 );
        }
    }
    else if (el.hasClass("sort-down"))
    {
        var el2 = row.next("tr");
        if (el2)
        {
            row.insertAfter( el2 );
        }
        
    }
    
    var list = $(".gear-sort").map(function(){return $(this).data("id");}).get();
    $.post("'.Url::to(['warehouse/store-order']).'", {data:list, _csrf: yii.getCsrfToken()});
    
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
$("body").on("click", ".arrow-up", function(){
    $(this).closest("tr").next("tr").find(".wrapper").slideUp(200);
});
$(document).on("pjax:complete", function() {
  var el = currentEl.next("tr").find(".wrapper");
  if (el) 
  {
    el.slideDown();
  }
});




// linijka z gearem = gear-row, id; gearid
// linijka z item gearem = gear-item-row, id: gearitemid
// linijka z item groupem = gear-item-case-row, id: groupid
// linijka z zewnętrznym sprzętem = gear-item-outer-row, id: itemouterid



function addGearRow(gear) {
    if ($(".gear-row[data-gearid=\'"+gear.id+"\']").length === 1) {
        return;
    }
    var arrow = "";
    var extra_class = "gear-no-items-row ";
    if (gear.no_items == 0) {
        arrow = "<span class=\'row-warehouse-out glyphicon glyphicon-arrow-down\'></span>";
        extra_class = "";
    }
    
    var new_row =   "<tr class=\'gear-row item-in-basket additional-items"+extra_class+"\' data-gearid=\'"+gear.id+"\' style=\'cursor:pointer;\' >" +
                        "<td>"+gear.id+arrow+"</td>" +
                        "<td><input class=\'gear-our\' data-gearid=\'"+gear.id+"\' type=\'checkbox\' checked></td>"+
                        "<td>0</td>"+
                        "<td>0</td>"+
                        "<td>"+gear.name+"</td>"+
                        "<td class=\'number-list-model-"+gear.id+"\'></td>"+
                        "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                        "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear.id+"\'></span></td>"+
                    "</tr>";

    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });
    
}

function addGearNoItemsRow(gearModel, gear_id, row, number)
{
            if (number >0) {
            }
            else {
                if (row.length === 0) {
                    var numb = 0;
                }else{
                    var numb = parseInt(row.find(":nth-child(3)").html());
                }
                numb++;
                number = numb;
            }
           items[gear_id] = number;

            var checkbox = $(".itemnoitems[value=\'"+gear_id+"\']");
            if (!checkbox.prop("checked")) {
                checkbox.prop("checked", true);
            }
        
        
            if (row.length === 0) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gear_id,
                    success: function(gear) {
                        var new_row =   "<tr class=\'gear-no-items-row additional-items item-in-basket\' data-gearid=\'"+gear.id+"\' style=\'cursor:pointer;\' >" +
                                            "<td>"+gear.id+"</td>" +
                                            "<td><input class=\'gear-our\' data-gearid=\'"+gear_id+"\' type=\'checkbox\' checked></td>"+
                                            "<td class=\'gear-item-no-items-number\' data-gearid=\'"+gear_id+"\'>"+number+"</td>"+
                                            "<td>0</td>"+
                                            "<td>"+gear.name+"</td>"+
                                            "<td class=\'number-list-model-"+gear_id+"\'></td>"+
                                            "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                                            "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear_id+"\'></span></td>"+
                                        "</tr>";
                    
                        if ($("#outcomes-table tbody").length === 0) {
                            $("#outcomes-table").append("<tbody></tbody>");
                        }
                        $("#outcomes-table tbody").each(function(index){
                            if (index === 0) {
                                $(this).append(new_row);
                            }
                        });  
                    }
                });
            
            }
            else {
                if (!row.find("input").prop("checked")) {
                    //row.find("input").trigger("click");
                    row.find("input").prop("checked", true);
                }
                if (number != null) {
                    row.find(":nth-child(3)").html(number);
                }
                else {
                    //var numb = parseInt(row.find(":nth-child(3)").html());
                    //numb++;
                    row.find(":nth-child(3)").html(number);
                }
                scannedTd = parseInt(row.find(":nth-child(4)").html());
                if (number>=scannedTd)
                        row.addClass("item-in-basket");
                if (scannedTd<number)
                    row.addClass("more-items");
                } 
}

function addGearNoItems(gear_id, number) { 

    $.ajax({
        url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gear_id,
        async: false,
        success: function(resp) {
            var gearModel = resp;
            var row = $(".gear-no-items-row[data-gearid=\'"+gearModel.id+"\']"); 
            if (number!=null)
                addGearNoItemsRow(gearModel, gear_id, row, number);
            else{
                if (row.is(":empty"))
                {
                    var ile = 1;
                }else{
                    var ile = row.find(":nth-child(4)").html();
                }
                        swal({
                          text: "Podaj liczbę sztuk "+gearModel.name,
                          content: {
                            element: "input",
                            attributes: {
                              placeholder: "Podaj wartość",
                              type: "number",
                              value:ile
                            }
                        },
                          button: {
                            text: "OK",
                            closeModal: true,
                          },
                        })
                        .then(name => {
                          if (!name) name=ile;
                            number = name;
                            addGearNoItemsRow(gearModel, gear_id, row, number);
                        });
            }
            
        }
    });
 
}

function removeGearNoItems(gear_id) {
    var row = $(".gear-no-items-row[data-gearid=\'"+gear_id+"\']"); 
    if (row.find("input").prop("checked")) {
        row.find("input").prop("checked", false);
        row.removeClass("item-in-basket");
    }
    var input = $(".itemnoitems[value=\'"+gear_id+"\']");
    if (input.prop("checked")) {
        input.prop("checked", false);
    }
    eraseCookie("checkbox-item-gear[" + gear_id + "]");
}

function addCaseRfid(case_id, with_case) {
    // dodajemy case z urzadzeniami - urzadzenia nie sa zaznaczone
    groups[case_id] = 1;
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-group']).'?gear_id=" + case_id,
        success: function(gearGroup) {
            
            // kiedy nie ma rowa z gearem
            if( $(".gear-row").find("[data-gearid=\'"+gearGroup.gear_ids[0]+"\']").length == 0 ) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "'.Url::to(['outcomes-warehouse/get-gear']).'?gear_id=" +gearGroup.gear_ids[0],
                    success: function(gear) {
                        addGearRow(gear);
                        addGroupRowRfid(gearGroup);
                    }
                });
            }
            // kiedy jest row z gearem
            else {
                addGroupRowRfid(gearGroup);
            }
        }
    });
    if (with_case) {
        addToCase(null, true, case_id);
    }
}

// dodajemy gear item, który należy do case
function addGearItemToCase(gear) {
    // tutaj dodajemy pusty case
    addCaseRfid(gear.group_id, false);
    
    // zaznaczamy sprzęt, który się zeskanował
    if ($.inArray(parseInt(gear.id), $("body").find(".gear-item-case-row[data-groupid=\'"+gear.group_id+"\']").data("added")) == -1) {
        createCookie("checkbox-item-gear[" + gear.id + "]", 1, 1);
        addToCase(gear, null, gear.group_id);
    }
}

function addToCase(gear, add_case, group_id) {
    var row = $(".gear-item-case-row[data-groupid=\'"+group_id+"\']");

    var numbers;
    // z dupy
    var new_ids = [1,2,3,4,5,6,7];
    if (gear != null) {
        var added = [];
        var added_numbers = [];

        if(row.data("added")) { 
            added = row.data("added");
            added_numbers = row.data("added_numbers");
        }
        if ($.inArray(parseInt(gear.id), added) == -1) {
            added.push(parseInt(gear.id));
            added_numbers.push(parseInt(gear.number));
        }
        row.data("added", added);
        row.data("added_numbers", added_numbers);
         
        var numberTd = $(".gear-row[data-gearid=\'"+gear.gear_id+"\']").find("td:nth-child(4)");
        var number = parseInt(numberTd.html());
        number++;
        numberTd.html(number);
        
        ids = [];
        if (row.data("item-ids")) {
            ids = row.data("item-ids");
        }
        var new_ids = [];
        for (var i = 0; i < ids.length; i++) {
            if (gear.number != ids[i]) {
                new_ids.push(ids[i]);
            }
        }
        row.data("item-ids", new_ids);
        
        numbers = "[<span class=\'item-in-basket\'>";
        for (var i = 0; i < added.length; i++) {
            numbers += added_numbers[i]+", ";
        }
        numbers += "</span><span class=\'orangered\'>";
        for (var i = 0; i < new_ids.length; i++) {
            numbers += new_ids[i]+", ";
        }
        numbers += "</span>]";
        row.find("td:nth-child(5)").removeClass("orangered");
        row.find("td:nth-child(5)").html(numbers);
    }
    
    if (new_ids.length == 0) {
        row.find("td:nth-child(5)").addClass("item-in-basket");
        row.find("td:nth-child(5)").removeClass("orangered");
    }
    
    if (add_case == true) {
        row.data("case", true);
        row.find("td:nth-child(3)").addClass("item-in-basket");
    }
    
    if (row.data("case") == true && row.data("item-ids").length == 0 && !row.hasClass("item-in-basket")) {
        row.find("td:nth-child(5)").removeClass("orangered");
        row.find("input").trigger("click")    
    }
}

function addGroupRowRfid(group) {
    var modelRow = $(".gear-row[data-gearid=\'"+group.gear_ids[0]+"\']");
    var groupRow = $(".gear-item-case-row[data-groupid=\'"+group.id+"\']");
    if (modelRow.length == 0) {
        alert("'.Yii::t('app', 'Błąd, nie ma modelu w tabelce!').'");
        return;
    }
    // nie dodajemy, bo już jest row z tym casem
    if (groupRow.length == 1) {
        return;
    }
    
    if (modelRow.next().hasClass("sub_models")) {
        modelRow.next().find("tbody").append(gearGroupRfid(group));
    }
    else {
        modelRow.after(
            "<tr class=\'sub_models\' style=\'display: none;\'>"+
                "<td colspan=\'9\'>"+
                    "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                        "<thead><tr><td>'.Yii::t('app', 'Id').'</td><td>'.Yii::t('app', 'Wydanie').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                        "<tbody>"+gearGroupRfid(group)+"</tbody>"+
                "</td>"+
            "</tr>"
        );
    }
    modelRow.find("input").trigger("click");
}

function gearGroupRfid(group) {
    var numbers;
    var numer_list = "";
    var ids = [];
    for (var i = 0; i < group.items.length; i++) {                        
        numbers += "<span class=\'item_in_case_number_" + group.items[i].number + "\'>" + group.items[i].number + ",</span> ";
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
        numbers = "[" + Math.min.apply(null, ids) + " - " + Math.max.apply(null, ids) + "]";
    }

    return "<tr class=\'checkbox-group  gear-item-case-row \' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\' data-item-ids=\'["+ids+"]\'  data-case=\'false\'>"+
                "<td>"+group.id+"</td>"+
                "<td><input class=\'gear-group\' data-id=\'"+group.id+"\' data-gearid=\'+group.items[0].gear_id+\' data-numbers=\'"+numbers+"\' type=\'checkbox\' ></td>"+
                "<td><img src=\'/admin/../img/case.jpg\' alt=\'\' style=\'width:100px;\' ></td>"+
                "<td>"+group.items[0].name+"</td>"+
                "<td class=\'orangered\'>"+numbers+"</td>"+
                "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\' data-itemno=\'"+group.items.length+"\'></span></td>"+
            "</tr>";
}

function addGearItem(gear) {
    var modelRow = $(".gear-row[data-gearid=\'"+gear.gear_id+"\']");
    var itemRow = $(".gear-item-row[data-gearitemid=\'"+gear.id+"\']");

    // jeżeli jest linijka z modelem
    if (modelRow.length === 1) {

        if (gear.group_id != null) {
            //addGearItemToCase(gear);
            //return;            
        }
        if (gear.name == "_ILOSC_SZTUK_") {
            addGearNoItems(gear.id, null);
            return;
        }
        
        // jeżeli juz jest, to nie dodajemy
        if(itemRow.length == 1) {
            var checkbox = itemRow.find("input");
            if (!checkbox.is(":checked")) {
                checkbox.trigger("click");    
                var numberTd =  modelRow.find("td:nth-child(3)");
                numberTd.html((parseInt(numberTd.html())+1));        
            }
            return;
        }
       
        
        // jeżeli ma podmmodele (header tablki po rozwinięciu)
        if (modelRow.next().hasClass("sub_models")) {
            modelRow.next().find("tbody").append(gearItemRow(gear));
            var numberTd =  modelRow.find("td:nth-child(3)");
            numberTd.html((parseInt(numberTd.html())+1));
        }
        else {
            var numberTd =  modelRow.find("td:nth-child(3)");
            numberTd.html((parseInt(numberTd.html())+1));
            modelRow.after(
                "<tr class=\'sub_models\' style=\'display: none;\'>"+
                    "<td colspan=\'9\'>"+
                        "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                            "<thead><tr><td>'.Yii::t('app', 'Id').'</td><td>'.Yii::t('app', 'Wydanie').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                            "<tbody>"+gearItemRow(gear)+"</tbody>"+
                    "</td>"+
                "</tr>"
            );
        }
        
        if ($(".number-list-gear-"+gear.id).length === 0 ) {
            if (gear.status==10)
            {
                $(".number-list-model-" + gear.gear_id).append("<span class=\'item-in-basket in-service number-list-gear-"+gear.id+"\'><i class=\'fa fa-wrench\'></i> " +gear.number+", </span>");
            }else{
                $(".number-list-model-" + gear.gear_id).append("<span class=\'item-in-basket number-list-gear-"+gear.id+"\'>"+gear.number+", </span>");
            }
            
        }
        else {
            $(".number-list-gear-"+gear.id).addClass("item-in-basket in-service");
        }
        numberTd =  parseInt(modelRow.find("td:nth-child(4)").html());
        scannedTd = parseInt(modelRow.find("td:nth-child(3)").html());
        if (scannedTd>=numberTd)
            modelRow.addClass("item-in-basket");
        if (scannedTd>numberTd)
            modelRow.addClass("more-items");
    }
    else {
        alert("'.Yii::t('app', 'Błąd nr').': #000158qad666");
    }
}

function gearItemRow(gear) {
    return "<tr class=\'gear-item-row item-in-basket\' data-gearitemid=\'"+gear.id+"\'>"+
                "<td>"+gear.id+"</td>"+
                "<td><input class=\'gear-item-our\' data-id=\'"+gear.id+"\' data-gearid=\'"+gear.gear_id+"\' data-number=\'"+gear.number+"\' type=\'checkbox\' checked></td>"+
                "<td>"+gear.name+"</td>"+
                "<td><span class=\'checkbox-item-gear\' data-id=\'"+gear.id+"\'>"+gear.number+"</span></td>"+
                "<td><span class=\'remove_one_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+gear.id+"\'></span></td>"+
            "</tr>";
}

function addGearGroup(id) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-group']).'?gear_id=" + id,
        success: function(gearGroup) {
            groups[id] = 1;
            // jeżeli jest tylko jednego rodzaju sprzęt w case to dodajemy go w podkategorii tego sprzętu
            if (gearGroup.gear_ids.length === 1) {
                // kiedy nie ma rowa z gearem
                if( $(".gear-row").find("[data-gearid=\'"+gearGroup.gear_ids[0]+"\']").length == 0 ) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "'.Url::to(['outcomes-warehouse/get-gear']).'?gear_id=" +gearGroup.gear_ids[0],
                        success: function(gear) {
                            addGearRow(gear);
                            addGroup(gearGroup);
                        }
                    });
                }
                // kiedy jest row z gearem
                else {
                    addGroup(gearGroup);
                }
            }
            // inaczej case wyświetlamy samotnie
            else {
                addAloneCase(gearGroup);
            }
        }
    });
}

function addAloneCase(group) {
    var itemNames = "";
    for (var i = 0; i < group.items.length; i++) {
        itemNames +=  group.items[i].name + " number: " + group.items[i].number + "<br>";
    }
    var new_row =   "<tr class=\'checkbox-group  gear-item-case-row item-in-basket\' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\' >" +
                        "<td>"+group.id+"</td>" +
                        "<td><input class=\'gear-group\' data-id=\'"+group.id+"\' type=\'checkbox\' checked></td>"+
                        "<td><img src=\'/admin/../img/case.jpg\' alt=\'\' style=\'width: 100px;\' ></td>"+
                        "<td>1</td>"+
                        "<td>0</td>"+
                        "<td>"+itemNames+"</td>"+
                        "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                        "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\' data-itemno=\'"+group.items.length+"\'></span></td>"+
                    "</tr>";

    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });
}

function addGroup(group) {
    var modelRow = $(".gear-row[data-gearid=\'"+group.gear_ids[0]+"\']");
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
        numbers = "[" + Math.min.apply(null, ids) + " - " + Math.max.apply(null, ids) + "]";
    }

    if (modelRow.length === 1) {
        if (modelRow.next().hasClass("sub_models")) {
            if (groupRow.length === 1) {
                groupRow.find("input:checkbox").trigger("click");
            }
            else {
                modelRow.next().find("tbody").append(gearGroupRow(group));
               
            }
        }
        else {
            modelRow.after(
                "<tr class=\'sub_models\' style=\'display: none;\'>"+
                    "<td colspan=\'9\'>"+
                        "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                            "<thead><tr><td>'.Yii::t('app', 'Id').'</td><td>'.Yii::t('app', 'Wydanie').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                            "<tbody>"+gearGroupRow(group)+"</tbody>"+
                    "</td>"+
                "</tr>"
            );
        }
        var numberTd =  modelRow.find("td:nth-child(3)");
        numberTd.html((parseInt(numberTd.html())+group.items.length));
        if ($(".number-list-group-"+group.id).length === 0) {
            $(".number-list-model-" + group.gear_ids[0]).append("<span class=\'item-in-basket number-list-group-"+group.id+"\'>"+numbers+"</span>");
        }
        else {
            $(".number-list-group-"+group.id).addClass("item-in-basket");
        }
        numberTd =  parseInt(modelRow.find("td:nth-child(4)").html());
        scannedTd = parseInt(modelRow.find("td:nth-child(3)").html());
        if (scannedTd>=numberTd)
            modelRow.addClass("item-in-basket");
        if (scannedTd>numberTd)
            modelRow.addClass("more-items");
    }
    else {
        alert("'.Yii::t('app', 'Błąd nr').': #00017do%domu623478");
    }
}

function gearGroupRow(group) {
    var numbers;
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
        numbers = "[" + Math.min.apply(null, ids) + " - " + Math.max.apply(null, ids) + "]";
    }
    return "<tr class=\'checkbox-group  gear-item-case-row item-in-basket\' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\'>"+
                "<td>"+group.id+"</td>"+
                "<td><input class=\'gear-group\' data-id=\'"+group.id+"\' data-gearid=\'+group.items[0].gear_id+\' data-numbers=\'"+numbers+"\' data-quantity=\'"+ids.length+"\' type=\'checkbox\' checked></td>"+
                "<td>"+group.items[0].name+"</td>"+
                "<td>"+numbers+"</td>"+
                "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\' data-itemno=\'"+group.items.length+"\'></span></td>"+
            "</tr>";
}

function removeGearGroup(id) {
    $("body").find(".checkbox-group.gear-item-case-row[data-groupid=\'"+id+"\']").remove();
    $(".number-list-group-"+id).removeClass("item-in-basket");
}

function addRowGearItem(id) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-item']).'?gear_id=" + id,
        success: function(gearItem) {
            if (gearItem.no_items==1)
            {
                            $.ajax({
                                url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gearItem.id,
                                async: false,
                                success: function(resp) {
                                    var gearModel = resp;
                                    var row = $(".gear-no-items-row[data-gearid=\'"+gearModel.id+"\']"); 
                                    addGearNoItemsRow(gearModel, gearItem.id, row, 0);
                                    
                                    
                                }
                            });
            }else{
             // kiedy nie ma rowa z gearem
            items[gearItem.id] = 1;
            if( $(".gear-row").find("[data-gearid=\'"+gearItem.gear_id+"\']").length == 0 ) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "'.Url::to(['outcomes-warehouse/get-gear']).'?gear_id=" + gearItem.gear_id,
                    success: function(gear) {
                            addGearRow(gear);
                            addGearItem(gearItem); 
                    }
                });
            }else{
                            addGearItem(gearItem);
                }
            }


        }
    });
}
function removeRowGearItem(id) {
    var itemRow = $(".gear-item-row[data-gearitemid=\'"+id+"\']");
    var gearRow = itemRow.parent().parent().parent().parent().prev();
    var itemNo = parseInt(gearRow.find("td:nth-child(4)").html());
    $(".number-list-gear-"+id).removeClass("item-in-basket");
    itemNo--;
    gearRow.find("td:nth-child(4)").html(itemNo);
    itemRow.remove();
    if (itemNo === 0) {
        gearRow.next().remove();
        gearRow.remove();
    }
}


$(".checkbox-item-id").change(function(){
    if ($(this).is(":checked")) {
        if ($(this).hasClass("itemnoitems")) {
            if ($(this).parent().prev().find("input").val() != 0) {
                addGearNoItems($(this).val(), $(this).parent().prev().find("input").val());
            }   
            return;
        }
   
        createCookie("checkbox-item-gear[" + $(this).val() + "]", 1, 1);
        addRowGearItem($(this).val());
    }
    else {
        if ($(this).hasClass("itemnoitems")) {
            removeGearNoItems($(this).val());
            return;
        }
        
        eraseCookie("checkbox-item-gear[" + $(this).val() + "]");
        removeRowGearItem($(this).val());
    }
});


$(".checkbox-group").change(function(){
    if ($(this).is(":checked")) {
        createCookie("checkbox-group[" + $(this).val() + "]", 1, 1);
        addGearGroup($(this).val());
    }
    else {
        eraseCookie("checkbox-group[" + $(this).val() + "]");
        removeGearGroup($(this).val());
    }
});

// ************************

var new_page = true;
var clearCookies = false;
$(".category-menu-link").click(function(e){
    new_page = false;
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
    .substr(1)
        .split("&")
        .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
    return result;
}

function confirmExit() {
    if (new_page == true) {
        clearCookies = true;
        return "'.Yii::t('app', 'Czy na pewno opuścić stronę?').'";
    }
    else {
        clearCookies = false;
        new_page = true;
    }
}
function clearSession() {
     if (clearCookies == true) {
        clearCookies = false;
        clearAllCookies();
    }
}
function clearAllCookies() {
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var name = cookies[i].trim().split("=")[0];
         document.cookie = name + \'=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/\';
    }
}
window.onbeforeunload = function() { return confirmExit(); }
window.onunload = function() { return clearSession(); }

// ************************

function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}
function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(\';\');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === \' \') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}
function eraseCookie(name) {
    createCookie(name, "", -1);
}


$(".header-checkbox").change(function(){
    $(this).parent().parent().parent().parent().find(".checkbox-item-id").each(function(){
        if ($(this).is(":checked")) {
            createCookie("checkbox-item-gear[" + $(this).val() + "]", 1, 1);
            addRowGearItem($(this).val());
        }
        else {
            eraseCookie("checkbox-item-gear[" + $(this).val() + "]");
            removeRowGearItem($(this).val());
        }
    });
});


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

$(".number_no_items").change(function(){
    if ($(this).val() == 0) {
        removeGearNoItems($(this).parent().next().find("input").val());
        $(this).parent().next().find("input").prop("checked", false);
    }
    else {
        addGearNoItems($(this).parent().next().find("input").val(), $(this).val());
        $(this).parent().next().find("input").prop("checked", true);
    }
});

$("body").on("change", ".gear-no-items-row .gear-our", function(){
    if ($(this).prop("checked")) {
        addGearNoItems($(this).data("id"), $(this).parent().next().next().html());
    }
    else {
        removeGearNoItems($(this).data("id"));
    }
});


function addOuterGearItem(id, number) {
    var gear_row = $(".gear-item-outer-row[data-itemouterid=\'"+id+"\']");
    if ( gear_row.length == 1) {
        gear_row.addClass("item-in-basket");
        gear_row.find("input:checkbox").prop("checked", true);
        var item_no = parseInt(gear_row.find("td:nth-child(4)").html());
        item_no += number;
        gear_row.find("td:nth-child(4)").html(item_no);
    }
    else {
        createRowOuterGear(id, number);
    }
}

function createRowOuterGear(id, number) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-item-outer']).'?gear_id=" + id,
        success: function(gear) {
        
            var img;
            if (gear.photo) {
                img = "<img src=\'/uploads/outer-gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
            }
            var new_row = 
                "<tr class=\'gear-item-outer-row item-in-basket\' data-itemouterid=\'"+id+"\'>"+
                    "<td>"+id+"</td>"+
                    "<td><input class=\'gear-item-outer\' data-id=\'"+id+"\' type=\'checkbox\' checked></td>"+
                    "<td>"+img+"</td>"+
                    "<td>"+number+"</td>"+
                    "<td>0</td>"+
                    "<td>"+gear.name+"<br>"+gear.qrcode+"</td>"+
                    "<td></td>"+
                    "<td>'.Yii::t('app', 'Zewnętrzny').'</td>"+
                    "<td><span class=\'remove_outer_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+id+"\'></span></td>"+
                "</tr>";
            
            if ($("#outcomes-table tbody").length === 0) {
                $("#outcomes-table").append("<tbody></tbody>");
            }
            $("#outcomes-table tbody").each(function(index){
                if (index === 0) {
                    $(this).append(new_row);
                }
            });
        }
    });
}

');


$this->registerCss('
.container-working-time button { color: red; }
.number_no_items { width: 50px; }
.orangered { color: orangered; }
.item-in-basket.additional-items{ background-color: #f6be81;}
.item-in-basket.more-items{ background-color: #f6f581; }
.item-in-basket.additional-items.more-items{ background-color: #f6be81;}
.item-in-basket.in-service{ background-color: #990000; color:white;}
');

?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function addNumberNoItem()
    {
        swal({
          text: 'Podaj liczbę sztuk',
          content: {
            element: "input",
            attributes: {
              placeholder: "Podaj wartość",
              type: "number",
              value:1
            }
        },
          button: {
            text: "OK",
            closeModal: true,
          },
        })
        .then(name => {
          if (!name) throw null;
            x = name;
        });
    }
</script>