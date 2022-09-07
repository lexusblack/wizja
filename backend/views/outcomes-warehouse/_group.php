<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\models\EventGearItem;
use common\models\Event;
use common\models\OutcomesGearOur;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

// Case list

$event = isset($event) ? $event : null;
$rent = isset($rent) ? $rent : null;
?>

<?php if ($warehouse->showGroups == true): ?>
    <div class="gear gear-groups">
        <?= GridView::widget([
            'dataProvider' => $warehouse->gearGroupDataProvider,
            'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
            'layout'=>'{items}',
            'filterModel' => null,
            'options' => ['class'=>'warning'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => '',
                    'checkboxOptions' => function ($model, $key, $index, $column) use ($event) {
                        $checked = false;
                        $disabled = false;
                        if (isset($_COOKIE['checkbox-group'][$model->id])) {
                            $checked = true;
                        }
                        if ($model->numberOfAvailable() <= 0) {
                            $disabled = true;
                        }
                        return [
                            'checked' => $checked,
                            'disabled' => $disabled,
                            'class'=>'checkbox-group'
                        ];
                    },
                ],
                [
                    'content'=>function($model, $key, $index, $grid) use ($warehouse)
                    {

                        $activeGroup = $warehouse->activeGroup;
                        $icon = $activeGroup==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeGroup==$model->id ?  null : $model->id;
                        return Html::a(Html::icon($icon), Url::current(['activeGroup'=>$id]), ['class'=> "category-menu-link " . $icon]);


                    },
                    'contentOptions'=>['class'=>'text-center'],
                ],
                [
                    'content'=>function()
                    {
                        return Html::img('@web/../img/case.jpg', ['style'=>'width:100px;']);
                    }
                ],
                [
                    'header' => Yii::t('app', 'Numery urządzeń'),
                    'value'=>'itemNumbers',
                ],
                [
                    'header' => Yii::t('app', 'Numer QR/Bar'),
                    'value'=>function($model) {
                        return $model->getBarCodeValue();
                    },
                ],
                [
                    'header' => Yii::t('app', 'Rezerwacje'),
                    'format' => 'raw',
                    'value' => function ($group) use ($event, $rent) {
                        if ($group->numberOfAvailable() == 0) {
                            $outcome = null;
                            foreach ($group->gearItems as $item) {
                                $outcome = OutcomesGearOur::find()->where(['gear_id'=>$item->id])->one()->outcome;
                                if ($outcome) {
                                    break;
                                }
                            }

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
                        if ($event)
                        {
                            $start = new DateTime($event->getTimeStart());
                            $end = new DateTime($event->getTimeEnd());                            
                        }
                        if ($rent)
                        {
                            $start = new DateTime($rent->getTimeStart());
                            $end = new DateTime($rent->getTimeEnd());                           
                        }
                        $negativeInterval = new DateInterval("P1D");
                        $negativeInterval->invert = 1;
                        $start->add($negativeInterval);
                        $end->add(new DateInterval("P1D"));
                        $gearItem = $group->gearItems[0];

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
                                    'action'=>['event/update-working-time-event-gear-group', 'eventId'=>$eventGear->event_id, 'group' => $group->id],
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
                                        'group' => $group->id,
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
                                                    url: '".Url::to(['event/update-working-time-event-gear-group'])."?eventId=' +button.data(\"eventid\") + '&group=' + button.data(\"group\"),
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
                [
                    'header' => Yii::t('app', 'Stan'),
                    //z użądzeń wyciągnąć stany.
                ],
                [
                    'label'=>Yii::t('app', 'Ilość urządzeń'),
                    'content'=>function($model, $key, $index, $grid)
                    {
                        return $model->getItemsCount();
                    }

                ],
                'location',
                'weight',
                'width',
                'height',
                'depth',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'urlCreator' =>  function($action, $model, $key, $index)
                    {
                        $params = is_array($key) ? $key : ['id' => (string) $key];
                        $params[0] = 'gear-group/' . $action;

                        return Url::toRoute($params);
                    }
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