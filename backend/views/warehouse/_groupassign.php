<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\models\EventGearItem;
use common\models\Event;
use common\models\GearItem;
use common\models\Rent;
use common\models\RentGearItem;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
//use yii\grid\GridView;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

$event = isset($event) ? $event : null;

?>

<?php if ($warehouse->showGroups == true): ?>
    <div class="gear gear-groups">
<!--        <h3>Case</h3>-->
        <?= GridView::widget([
            'dataProvider' => $warehouse->gearGroupDataProvider,
            'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
            'layout'=>'{items}',
            'filterModel' => null,
            'options' => ['class'=>'warning'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) use ($event) {
                        return [
                            'checked' => $model->getIsGroupAssigned($event),
                            'class'=>'checkbox-group',
                            'data'=> ['gearid'=>$model->gearItems[0]->gear_id],
                            'disabled' => $model->gearItems[0]->isAvailable($event) ? false : true,
                        ];
                    },
                    'visible'=>$checkbox,
                ],
                [
                    'content'=>function($model, $key, $index, $grid) use ($warehouse)
                    {

                        $activeGroup = $warehouse->activeGroup;
                        $icon = $activeGroup==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeGroup==$model->id ?  null : $model->id;
                        return Html::a(Html::icon($icon), Url::current(['activeGroup'=>$id]), ['class'=>$icon]);


                    },
                    'contentOptions'=>['class'=>'text-center'],
                ],
                [
                    'content'=>function()
                    {
                        return Html::img('@web/../img/case.jpg', ['style'=>'width:100px;']);
                    }
                ],
                'name',
                [
                    'header' => Yii::t('app', 'Numery urządzeń'),
                    'format' => 'html',
                    'value' => function($model) {
                        $numbers = $model->itemNumbers;
                        foreach ($model->gearItems as $item) {
	                        if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
		                        $numbers .= "<br>".Html::tag('span', Yii::t('app', 'W serwisie numer').': '.$item->number, ['class' => 'label label-danger']);
	                        }
                        }
                        return $numbers;
                    }
                ],
                [
                    'header' => Yii::t('app', 'Rezerwacje'),
                    'format' => 'raw',
                    'value' => function ($group) use ($event) {
                        if ($event) {
                            $start = new DateTime($event->getTimeStart());
                            $end = new DateTime($event->getTimeEnd());
                            $negativeInterval = new DateInterval("P1D");
                            $negativeInterval->invert = 1;
                            $start->add($negativeInterval);
                            $end->add(new DateInterval("P1D"));
                            $gearItem = $group->gearItems[0];

                            $working1 = EventGearItem::find()->where(['>', 'end_time',
                                    $start->format("Y-m-d H:i:s")])->andWhere(['<=', 'end_time',
                                    $end->format("Y-m-d H:i:s")])->andWhere(['gear_item_id' => $gearItem->id])->all();
                            $working2 = EventGearItem::find()->where(['<', 'start_time',
                                    $end->format("Y-m-d H:i:s")])->andWhere(['>=', 'end_time',
                                    $end->format("Y-m-d H:i:s")])->andWhere(['gear_item_id' => $gearItem->id])->all();
                            $working = array_merge($working1, $working2);
                            $result = "";
                            foreach ($working as $eventGear) {
                                $showed[] = [$eventGear->start_time, $eventGear->end_time];
                                $display_value = $eventGear->start_time . " - " . $eventGear->end_time;
                                $result .= Editable::widget(['formOptions' => ['action' => ['event/update-working-time-event-gear-group',
                                    'eventId' => $eventGear->event_id, 'group' => $group->id],], 'asPopover' => true,
                                    'placement' => PopoverX::ALIGN_RIGHT, 'inputType' => Editable::INPUT_DATE_RANGE,
                                    'header' => Yii::t('app', 'Czas pracy'), 'size' => PopoverX::SIZE_LARGE, 'model' => $eventGear,
                                    'attribute' => 'dateRange', 'displayValue' => $display_value,
                                    'submitButton' => ['icon' => Html::icon('ok'),
                                        'class' => 'btn btn-sm btn-primary change-working-time-period',
                                        'data' => ['eventid' => $eventGear->event_id, 'gearid' => $gearItem->gear_id,
                                            'itemid' => $gearItem->id, 'group' => $group->id,],],
                                    'containerOptions' => ['style' => 'display: inline-block; white-space: nowrap;',
                                        'class' => 'container-working-time'], 'options' => [

                                        'id' => 'edit-' . $gearItem->id . '-' . $eventGear->event_id,
                                        'options' => ['style' => 'width: 100%',
                                            'id' => 'picker-' . $gearItem->id . '-' . $eventGear->event_id,
                                            'class' => 'form-controll'], 'convertFormat' => true,
                                        'startAttribute' => 'start_time', 'endAttribute' => 'end_time',
                                        'pluginOptions' => ['timePicker' => true, 'timePickerIncrement' => 5,
                                            'timePicker24Hour' => true, 'locale' => ['format' => 'Y-m-d H:i:s'],],],
                                    'pluginEvents' => ["editableBeforeSubmit" => "function(event, jqXHR) { 
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
                                                        url: '" . Url::to(['event/update-working-time-event-gear-group']) . "?eventId=' +button.data(\"eventid\") + '&group=' + button.data(\"group\"),
                                                        success: function(response) {
                                                            $('#edit-' + button.data('itemid') + '-' + button.data('eventid') + '-targ').html(inputText);
                                                            $('.close').trigger('click');
                                                            location.reload();
                                                        }
                                                    });
                                                    
                                                }",],]);
                                $gearEvent = Event::find()->where(['id' => $eventGear->event_id])->one();
                                $result .= Html::a("<div>" . $gearEvent->name . "</div>", ['event/view',
                                        'id' => $eventGear->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                        'class' => 'linksWithTarget', 'data-pjax' => 0, 'style' => 'color:red;']);
                            }

                            $working_rents = [];
                            foreach ($group->gearItems as $gearItem) {
                                $rents1 = RentGearItem::find()->where(['>', 'end_time',
                                        $start->format("Y-m-d H:i:s")])->andWhere(['<=', 'end_time',
                                        $end->format("Y-m-d H:i:s")])->andWhere(['gear_item_id' => $gearItem->id])->all();
                                $rents2 = RentGearItem::find()->where(['<=', 'start_time',
                                        $end->format("Y-m-d H:i:s")])->andWhere(['>=', 'end_time',
                                        $end->format("Y-m-d H:i:s")])->andWhere(['.gear_item_id' => $gearItem->id])->all();
                                $working_rents = array_merge($working_rents, $rents1);
                                $working_rents = array_merge($working_rents, $rents2);
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
                    }
                ],
                [
                    'header' => Yii::t('app', 'Godziny lamp'),
                    'format' => 'html',
                    'value' => function ($group) {
                                $lamp_hours = "";
                                foreach ($group->gearItems as $item)
                                {
                                    if ($item->lamp_hours)
                                    {
                                        $lamp_hours .= Yii::t('app', "nr")." ".$item->number." - ".$item->lamp_hours."<br/>";
                                    }
                                    
                                }
                                return $lamp_hours;
                    },                   
                ],
                [
                    'header' => Yii::t('app', 'Uwagi'),
                    'format' => 'html',
                    'contentOptions' => function ($group) {
                        if ($group->getItemsInfo()!="")
                            return ['style'=>'background-color:#ed5565; color:white; white-space:nowrap; cursor_pointer;', 'class' => 'info'];
                        else
                            return [];
                    },
                    'value' => function ($group) {
                                $info = $group->getItemsInfo();
                                if ($info!="")
                                {
                                     $info_div ="<div class='display_none'>".$info."</div><span>".Yii::t('app', 'Pokaż uwagi')."</span>";
                                    return $info_div;                                   
                                }else{
                                    return "";
                                }

                    },                   
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
                    'label'=>Yii::t('app', 'Liczba urządzeń'),
                    'content'=>function($model, $key, $index, $grid)
                    {
                        return $model->getItemsCount();
                    }

                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}{update}{delete}',
                    'urlCreator' =>  function($action, $model, $key, $index)
                    {
                        $params = is_array($key) ? $key : ['id' => (string) $key];
                        $params[0] = 'gear-group/' . $action;

                        return Url::toRoute($params);
                    },
                    'visibleButtons' => [
                        'view' => Yii::$app->user->can('gearCaseView'),
                        'update' => Yii::$app->user->can('gearCaseEdit'),
                        'delete' => Yii::$app->user->can('gearCaseDelete'),
                    ]
                ],

            ],
            'afterRow' => function($model, $key, $index, $grid) use ($warehouse, $gearColumns, $checkbox)
            {
                $content = $this->render('_groupItems', [
                    'model'=>$model,
                    'activeGroup'=>$warehouse->activeGroup,
                    'gearGroupItemDataProvider'=>$warehouse->getGearGroupItemDataProvider(),
                    'checkbox'=>$checkbox,
                ]);

                $content = Html::tag('div', $content, ['class'=>'wrapper']);

                return Html::tag('tr', Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]));
            },
        ]); ?>
    </div>
<?php endif; ?>