<?php
/* @var $this \yii\web\View */
/* @var $event \common\models\Event */
/* @var $warehouse \common\models\form\WarehouseSearch; */
use common\models\EventGearItem;
use common\models\EventExtraItem;
use common\models\EventOuterGearModel;
use common\models\GearItem;
use common\models\Event;
use common\components\grid\GridView;
use common\models\OfferGear;
use common\models\OfferOuterGear;
use common\models\Rent;
use common\models\RentGearItem;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);

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
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Brak dostępnych egzemplarzy')."</h4>",
    'id' => 'similar_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ],
    'size' => 'modal-lg'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Dodaj wszystko - raport')."</h4>",
    'id' => 'add-all_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ],
    'size' => 'modal-lg'
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<div class="row">
        <div class="col-md-3">
        <?php 
                if ($type=='event')
                     echo Html::a(Html::icon('arrow-left').' '.Yii::t('app', 'Powrót do wydarzenia'), ['/event/view', 'id'=>$_GET['event_id'], '#'=>'tab-gear'], ['class'=>'btn btn-warning']);
                 else
                     echo Html::a(Html::icon('arrow-left').' '.Yii::t('app', 'Powrót do wypożyczenia'), ['/rent/view', 'id'=>$_GET['event_id']], ['class'=>'btn btn-warning']);
                 
                 echo " ".Html::a(Yii::t('app', 'Dodaj wszystko'), ['#'], ['class'=>'btn btn-primary gear-quantity-all']);
                 ?>
        </div>
        <?php if ($type=='event'){
            if (isset($packlist_id))
            {
                $packlist = \common\models\Packlist::findOne($packlist_id);
            }else{
                $packlist = \common\models\Packlist::find()->where(['event_id'=>$event->id])->orderBy(['main'=>SORT_DESC])->one();
            }

            $eventGearConnectedUrl = Url::to(['warehouse/assign-gear-connected', 'id'=>$event->id, 'type'=>$type]);
            $eventGearSimilarUrl = Url::to(['warehouse/gear-similar', 'id'=>$event->id]);
            $saveSimilarUrl = Url::to(['warehouse/save-similar', 'id'=>$event->id, 'packlist_id'=>$packlist->id]);
            $saveConflictUrl = Url::to(['warehouse/save-conflict', 'id'=>$event->id, 'packlist_id'=>$packlist->id]);
        
$packlists = \common\helpers\ArrayHelper::map(\common\models\Packlist::find()->where(['event_id'=>$event->id])->andWhere(['IS NOT', 'start_time', null])->andWhere(['blocked'=>0])->asArray()->all(), 'id', 'name');
            ?>
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
        <?php } ?>
    </div>
<?php
if (!$packlist->start_time)
{
    //komunikat, że packlista nie ma czasu
    ?>
    <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                            <div class="alert alert-danger">
                                <?=Yii::t('app', 'Wybrana grupa sprzętowa nie posiada zdefiniowanych godzin pracy, dlatego nie jest możliwe przypisanie do niej sprzętu. Godziny pracy zmienisz w zakładce sprzęt.')?>
                            </div>
                    </div>
                </div>
            </div>
            </div>
    <?php
}else{

// echo $this->render('_categoryMenu');

echo $this->render('_offers_menu', ['offers'=>$offers]);
$this->title = Yii::t('app', 'Przypisz sprzęt z oferty');
?>
<div class="warehouse-container">

<!--    --><?php //Pjax::begin([
//        'id'=>'warehouse-pjax-container',
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
                'label' => Yii::t('app', 'Ilość w ofercie'),
                'format' => 'raw',
                'value' => function ($model) use ($offers) {
                    $quantity = 0;
                    $offerResults = [];
                    foreach ($offers as $offer) {
                        $offerGears = OfferGear::find()->where(['gear_id' => $model->id])->andWhere(['offer_id' => $offer->id])->all();
                        foreach ($offerGears as $offerGear) {
                            $offerResults[$offerGear->offer->id] = [$offerGear->quantity, $offerGear->offer->name];
                            $quantity += $offerGear->quantity;
                        }
                    }
                    foreach ($offerResults as $id => $arr) {
                        if (count($offerResults) == 1) {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']);
                        }
                        else {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']) . " (" . $arr[0] .")";
                        }
                    }
                    return "<span style='white-space: nowrap;'>" . $quantity . "</span>";
                }
            ],

            [
                'label' => Yii::t('app', 'Pozostało'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($event, $offers, $type) {
                        
                    $offerGears = [];
                    foreach ($offers as $offer) {
                        $offerGear = OfferGear::find()->where(['gear_id' => $model->id])->andWhere(['offer_id' => $offer->id])->all();
                        if ($offerGear) {
                            $offerGears = array_merge($offerGears, $offerGear);
                        }
                    }
                    if (!$offerGears) {
                        return null;
                    }

                    $quantity = 0;
                    foreach ($offerGears as $offerGear) {
                        $quantity += $offerGear->quantity;
                    }
                    if ($type=='event')
                    {
                        $quantity = $quantity - $model->numberAssignedToEvent($event);
                        $quantity = $quantity - $model->numberInConflicts($event);
                    }
                    else
                        $quantity = $quantity - $model->numberAssignedToRent($event);
                    if ($quantity < 0) {
                        $quantity = 0;
                    }
                    if ($model->getConnectedNoOffer()>0)
                    {
                        $special = " do-not-add";
                    }else{
                        $special = "";
                    }

                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignmentPacklist();
                       // $assignmentForm->warehouse = $warehouse;
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $isAvailable = $model->getAvailabe($event->getTimeStart(), $event->getTimeEnd());

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form'.$special,
                                'data-description'=>$quantity."x ".$model->name
                            ],
                            'action' =>['assign-gear', 'id'=>$event->id, 'type'=>$type],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        if ($type=='event')
                            echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $model->numberAssignedToEvent($event)]);
                        else
                            echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $model->numberAssignedToRent($event)]);
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'style' => 'width: 68px;',
                            'value' => $quantity,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Dodaj'), [
                            'class'=>'btn btn-default gear-quantity',
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();
                        if ($quantity == 0) {
                            return 0;
                        }
                        return $this->blocks['form'];
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
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Zarezerwowany'),
                'format' => 'raw',
                'value' => function ($model) use ($event, $warehouse, $type) {
                    $working = $model->getEvents($warehouse->from_date, $warehouse->to_date);
                    $workingNear = $model->getEventsNear($warehouse->from_date, $warehouse->to_date);
                    $result = "";
                    foreach ($working['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
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
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    if ($type=='event'){
                        $quantity = $model->numberInConflicts($event);
                        if ($quantity)
                        {
                            $result.="<br/>".Yii::t('app', 'Dodano konflikt na ').$quantity.Yii::t('app', 'szt.');
                        }
                    }
                    return $result;
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
                    if ($gear->type!=1)
                    {
                        return $gear->quantity;
                    }
                    $assigned = 0;
                    if ($gear->no_items)
                    {
                        return $gear->getAvailabe($warehouse->from_date, $warehouse->to_date)+$assigned;
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

                        return ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber)+$assigned . " " . $serwis;
                    }
                }
            ]
            ];
?>
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h2><?=Yii::t('app', 'Sprzęt wewnętrzny')?></h2>
            <?php
        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $warehouse->getGearDataProvider(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap offer-assign-table'
            ],
            'filterModel' => null,
            'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $eventModel, $warehouse, $assignedItems, $type, $event)
            {
                $activeModel = $warehouse->activeModel;
                $gear_model = $model;
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
                            /** @var \common\models\GearItem $model */
                            return [
                                'class'=> ($model->isAvailable($event) && !($model->status == GearItem::STATUS_SERVICE)) ? '' : 'danger',
                            ];
                        },
                        'filterModel' => null,
                        'columns' => [
                            [
                                'headerOptions' => [
                                ],
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) use ($assignedItems, $type, $event,$warehouse, $gear_model) {
                                    /* @var $model \common\models\GearItem */
                                    $isAvailable = $model->isAvailable($event);

                                    return [
                                        'checked' => key_exists($model->id, $assignedItems),
                                        'class'=>'checkbox-item',
                                        'disabled'=> ($model->isAvailable($event) && !($model->status == GearItem::STATUS_SERVICE)) ? false : true,
                                    ];
                                }
                            ],
                            'id',
                            [
                                'attribute' => 'name',
                                'format' => 'html',
                                'value' => function($item) {
                                    $service = null;
                                    if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                        $service = Html::tag('span', Yii::t('app', 'W serwisie'), ['class' => 'label label-danger']);
                                    }
                                    return $item->name . " " . $service;
                                }
                            ],
                            'number:text:'.Yii::t('app', 'Nr'),

                            [
                                'label' => Yii::t('app', 'Zarezerwowany'),
                                'format' => 'raw',
                                'value' => function ($model) use ($eventModel) {
                                    $event = $eventModel;
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

                            'code:text:'.Yii::t('app', 'Kod'),
                            'serial:text:'.Yii::t('app', 'Nr seryjny'),
                            [
                                'attribute' => 'location',
                                'label' => Yii::t('app', 'Miejsce w<br/>magazynie'),
                                'encodeLabel'=>false,
                            ],
                        ],
                    ]);
                    $content = $this->render('_group', [
                            'checkbox'=>true,
                            'warehouse'=>$warehouse,
                            'gearColumns'=>$gearColumns,
                            'assignedItems'=>$assignedItems,
                            'type'=>$type,
                            'event'=>$event,
                        ]).$content;
                }
                else
                {
                    /* @var $model \common\models\Gear */

                    $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignmentPacklist();
                        $assignmentForm->warehouse = $warehouse;
                        $item = $model->getNoItemsItem();
                        $assignmentForm->itemId = $item->id;
                        $assignmentForm->quantity = key_exists($item->id, $assignedItems) ? $assignedItems[$item->id] : 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $isAvailable = $item->isAvailable($event);

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
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'disabled'=> $isAvailable ? false : true,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Zapisz'), [
                            'class'=>'btn btn-default gear-outer-quantity',
                            'disabled'=> $isAvailable ? false : true,
                        ]);
                        ActiveForm::end();
                    $this->endBlock();

                    $content .= $this->blocks['form'];
                    $rowOptions['class'] .= ($isAvailable ? '' : ' danger');
                }

                return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]), $rowOptions);
            },

            'columns' => $gearColumns,
        ]); 
        if ($exploGearDataProvider) { ?>
        <h2><?=Yii::t('app', 'Materiały eksploatacyjne na zamówienie')?></h2>
            <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $exploGearDataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap offer-assign-outer-table'
            ],
            'filterModel' => null,
            'columns'=>[
            [
                'label' => Yii::t('app', 'Ilość w ofercie'),
                'format' => 'raw',
                'value' => function ($model) use ($offers) {
                    $quantity = 0;
                    $offerResults = [];
                    foreach ($offers as $offer) {
                        $offerGears = OfferOuterGear::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['offer_id' => $offer->id])->all();
                        foreach ($offerGears as $offerGear) {
                            $offerResults[$offerGear->offer->id] = [$offerGear->quantity, $offerGear->offer->name];
                            $quantity += $offerGear->quantity;
                        }
                    }
                    foreach ($offerResults as $id => $arr) {
                        if (count($offerResults) == 1) {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']);
                        }
                        else {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']) . " (" . $arr[0] .")";
                        }
                    }
                    return "<span style='white-space: nowrap;'>" . $quantity . "</span>";
                }
            ],
            [
                'label' => Yii::t('app', 'Pozostało'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($event, $offers, $type) {
                        
                    $quantity = 0;
                    $oldQuantity = 0;
                    foreach ($offers as $offer) {
                        $offerGear = OfferOuterGear::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['offer_id' => $offer->id])->one();
                            $quantity += $offerGear->quantity;
                    }
                    $assigned = EventOuterGearModel::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['event_id' => $event->id])->one();
                    if ($assigned)
                    {
                        $quantity = $quantity-$assigned->quantity;
                        $oldQuantity = $assigned->quantity;
                    }
                        
                        $special = "";
                    

                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignment();
                       // $assignmentForm->warehouse = $warehouse;
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form'.$special,
                            ],
                            'action' =>['assign-outer-gear', 'id'=>$event->id, 'type'=>$type],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $oldQuantity]);
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'style' => 'width: 68px;',
                            'value' => $quantity,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Dodaj'), [
                            'class'=>'btn btn-default gear-outer-quantity',
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();
                        if ($quantity == 0) {
                            return 0;
                        }
                        return $this->blocks['form'];
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
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            ]
            ]); } if ($outerGearDataProvider){ ?>
        <h2><?=Yii::t('app', 'Sprzęt zewnętrzny')?></h2>
            <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $outerGearDataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap offer-assign-outer-table'
            ],
            'filterModel' => null,
            'columns'=>[
            [
                'label' => Yii::t('app', 'Ilość w ofercie'),
                'format' => 'raw',
                'value' => function ($model) use ($offers) {
                    $quantity = 0;
                    $offerResults = [];
                    foreach ($offers as $offer) {
                        $offerGears = OfferOuterGear::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['offer_id' => $offer->id])->all();
                        foreach ($offerGears as $offerGear) {
                            $offerResults[$offerGear->offer->id] = [$offerGear->quantity, $offerGear->offer->name];
                            $quantity += $offerGear->quantity;
                        }
                    }
                    foreach ($offerResults as $id => $arr) {
                        if (count($offerResults) == 1) {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']);
                        }
                        else {
                            $quantity .= "<br>" . Html::a($arr[1], Url::toRoute(['offer/default/view', 'id' => $id]), ['target' => '_blank']) . " (" . $arr[0] .")";
                        }
                    }
                    return "<span style='white-space: nowrap;'>" . $quantity . "</span>";
                }
            ],
            [
                'label' => Yii::t('app', 'Pozostało'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($event, $offers, $type) {
                        
                    $quantity = 0;
                    $oldQuantity = 0;
                    foreach ($offers as $offer) {
                        $offerGear = OfferOuterGear::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['offer_id' => $offer->id])->one();
                            $quantity += $offerGear->quantity;
                    }
                    $assigned = EventOuterGearModel::find()->where(['outer_gear_model_id' => $model->id])->andWhere(['event_id' => $event->id])->one();
                    if ($assigned)
                    {
                        $quantity = $quantity-$assigned->quantity;
                        $oldQuantity = $assigned->quantity;
                    }
                        
                        $special = "";
                    

                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignment();
                       // $assignmentForm->warehouse = $warehouse;
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form'.$special,
                            ],
                            'action' =>['assign-outer-gear', 'id'=>$event->id, 'type'=>$type],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $oldQuantity]);
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'style' => 'width: 68px;',
                            'value' => $quantity,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Dodaj'), [
                            'class'=>'btn btn-default gear-outer-quantity',
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();
                        if ($quantity == 0) {
                            return 0;
                        }
                        return $this->blocks['form'];
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
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            ]
            ]); } 

        if ($extraItemDataProvider) { ?>
        <h2><?=Yii::t('app', 'Sprzęt dodatkowy')?></h2>
            <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $extraItemDataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap offer-assign-extra-table'
            ],
            'filterModel' => null,
            'columns'=>[
            [
                'label' => Yii::t('app', 'Ilość w ofercie'),
                'format' => 'raw',
                'value' => function ($model) {
                    $quantity = "<br>" . Html::a($model->quantity." (".$model->offer->name.")", Url::toRoute(['offer/default/view', 'id' => $model->offer_id]), ['target' => '_blank']);

                    return "<span style='white-space: nowrap;'>" . $quantity . "</span>";
                }
            ],
            [
                'label' => Yii::t('app', 'Pozostało'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($event) {
                        
                    $quantity = $model->quantity;
                    $oldQuantity = 0;
                    $assigned = EventExtraItem::find()->where(['offer_extra_item_id' => $model->id])->one();
                    if ($assigned)
                    {
                        $quantity = $quantity-$assigned->quantity;
                        $oldQuantity = $assigned->quantity;
                    }
                        
                        $special = "";
                    

                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignment();
                       // $assignmentForm->warehouse = $warehouse;
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form'.$special,
                            ],
                            'action' =>['assign-extra-item', 'id'=>$event->id],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $oldQuantity]);
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'style' => 'width: 68px;',
                            'value' => $quantity,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Dodaj'), [
                            'class'=>'btn btn-default extra-item-quantity',
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();
                        if ($quantity == 0) {
                            return 0;
                        }
                        return $this->blocks['form'];
                }
            ],
            [
                'attribute' => 'section',
                'label' => Yii::t('app', 'Sekcja'),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->category_id)
                            return $model->category->name;
                        else
                            return "-";
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
            ],
            ]
            ]); }

            if ($produkcjaDataProvider) { ?>
        <h2><?=Yii::t('app', 'Produkcja')?></h2>
            <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $produkcjaDataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap offer-assign-extra-table'
            ],
            'filterModel' => null,
            'columns'=>[
            [
                'label' => Yii::t('app', 'W ofercie'),
                'format' => 'raw',
                'value' => function ($model) {
                    $quantity = "<br>" . Html::a($model->quantity." (".$model->offer->name.")", Url::toRoute(['offer/default/view', 'id' => $model->offer_id]), ['target' => '_blank']);

                    return "<span style='white-space: nowrap;'>" . $quantity . "</span>";
                }
            ],
            [
                'label' => Yii::t('app', 'Stwórz zadanie produkcyjne'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) use ($event) {
                        
                    $quantity = $model->quantity;
                    $oldQuantity = 0;
                    $assigned = EventExtraItem::find()->where(['offer_extra_item_id' => $model->id])->one();
                    if ($assigned)
                    {
                        $quantity = $quantity-$assigned->quantity;
                        $oldQuantity = $assigned->quantity;
                    }
                        
                        $special = "";
                    

                        $this->beginBlock('form');

                        $assignmentForm = new \common\models\form\GearAssignment();
                       // $assignmentForm->warehouse = $warehouse;
                        $assignmentForm->itemId = $model->id;
                        $assignmentForm->quantity = 0;
                        $assignmentForm->oldQuantity = $assignmentForm->quantity;

                        $form = ActiveForm::begin([
                            'options' => [
                                'class'=>'gear-assignment-form'.$special,
                            ],
                            'action' =>['assign-extra-item', 'id'=>$event->id, 'prod'=>true],
                            'type'=>ActiveForm::TYPE_INLINE,
                            'formConfig' => [
                                'showErrors' => true,
                            ]
                        ]);
                        echo Html::activeHiddenInput($assignmentForm, 'itemId');
                        echo Html::activeHiddenInput($assignmentForm, 'oldQuantity', ['value' => $oldQuantity]);
                        if ($assignmentForm->quantity === null) {
                            $assignmentForm->quantity = 1;
                        }
                        echo $form->field($assignmentForm, 'quantity')->textInput([
                            'style' => 'width: 68px;',
                            'value' => $quantity,
                        ]);
                        echo Html::submitButton(Yii::t('app', 'Dodaj'), [
                            'class'=>'btn btn-default prod-quantity',
                            'data' => ['gearid' => $model->id]
                        ]);
                        ActiveForm::end();
                        $this->endBlock();
                        if ($quantity == 0) {
                            return 0;
                        }
                        return $this->blocks['form'];
                }
            ],
            [
                'attribute' => 'section',
                'label' => Yii::t('app', 'Sekcja'),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->category_id)
                            return $model->category->name;
                        else
                            return "-";
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
            ],
            ]
            ]); }?>
    </div>
</div>
</div>

<?php } ?>

<?php
$eventGearUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGroupUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
$reloadUrl = Url::to(['warehouse/reload-quantity', 'packlist'=>$packlist->id]);
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";
$reload = Url::to(['warehouse/assign-gear-item-to-offer', 'event_id'=>$event->id]);
$this->registerJs('

$("#select-packlist").change(function(){
    location.href = "'.$reload.'&packlist="+$(this).val();
});
$(".grid-view-items :checkbox").not(".select-on-check-all, .checkbox-group").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventGear(id, add);
});
$(".grid-view-items :checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var elements = $(this).closest("table").find("tbody :checkbox").not(".checkbox-group");
    elements.each(function(index,el)
    {
        var id = $(el).val();
        var add = $(el).prop("checked");
        eventGear(id, add);
    });
    
});

$(".gear-groups .checkbox-group").not(".select-on-check-all").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventGroup(id, add);
});
$(".gear-groups :checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var elements = $(this).closest("table").find(".checkbox-group");
    elements.each(function(index,el)
    {
        var id = $(el).val();
        console.log(id);
        var add = $(el).prop("checked");
        eventGroup(id, add);
    });
    
});
function eventGroup(id, add)
{
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventGroupUrl.'", data, function(response){
        location.reload();
    });
}

function eventGear(id, add)
{
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventGearUrl.'", data, function(response){
        location.reload();
    });
}

function eventModel(id, add)
{
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventModelUrl.'", data, function(response){
        location.reload();
    });
}

$(":checkbox.checkbox-model").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventModel(id, add);
    
    var tr = $(this).closest("tr").next("tr");
    if (tr.hasClass("gear-details"))
    {
        tr.find(":checkbox").prop("checked", $(this).prop("checked"));
    }
    
    return false;
});

');
?>

<?php //Pjax::end(); ?>
<?php

$eventGearQuantityUrl = Url::to(['warehouse/assign-gear-packlist', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1, 'packlist'=>$packlist->id, 'offer'=>1]);
$eventExtraQuantityUrl = Url::to(['event-extra-item/assign', 'event_id'=>$event->id, 'packlist'=>$packlist->id]);
$prodQuantityUrl = Url::to(['event/create-prod-event', 'event_id'=>$event->id]);
$eventOuterGearQuantityUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$event->id, 'type'=>$type, 'noItem'=>0]);
$eventGearOuterConnectedUrl = Url::to(['outer-warehouse/manage-gear-connected', 'event_id'=>$event->id]);
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


$(".gear-quantity").on("click", function(e){
    e.preventDefault();
    $(this).attr("disabled", true);
    var form = $(this).closest("form");
    var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
    form.find("#gearassignment-quantity").val(val);
    var data = form.serialize();
    start = $("#warehouse_start").val();
    end = $("#warehouse_end").val();
    $.post("'.$eventGearQuantityUrl.'&start="+start+"&end="+end, data, function(response){
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
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
                if ((response.connected.length)||(response.outerconnected.length))
                {
                    showConnectedModal(response.connected, response.outerconnected);
                }
        } });
    
    return false;
});

$(".gear-outer-quantity").on("click", function(e){
    e.preventDefault();
    $(this).attr("disabled", true);
    var form = $(this).closest("form");
    var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
    form.find("#gearassignment-quantity").val(val);
    var data = form.serialize();
    var data = {"quantity":val, "itemid":form.find("#gearassignment-itemid").val(), "add":1};

    $.post("'.$eventOuterGearQuantityUrl.'", data, function(response){
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
        }
        else
        {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
        } });
    
    return false;
});

$(".extra-item-quantity").on("click", function(e){
    e.preventDefault();
    $(this).attr("disabled", true);
    var form = $(this).closest("form");
    var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
    form.find("#gearassignment-quantity").val(val);
    //var data = form.serialize();
    var data = {"quantity":val, "itemid":form.find("#gearassignment-itemid").val(), "add":1};
    $.post("'.$eventExtraQuantityUrl.'", data, function(response){
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
        }
        else
        {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
        } });
    
    return false;
});

$(".prod-quantity").on("click", function(e){
    e.preventDefault();
    $(this).attr("disabled", true);
    var form = $(this).closest("form");
    var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
    form.find("#gearassignment-quantity").val(val);
    //var data = form.serialize();
    var data = {"quantity":val, "itemid":form.find("#gearassignment-itemid").val(), "add":1};
    $.post("'.$prodQuantityUrl.'", data, function(response){
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
        }
        else
        {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
        } });
    
    return false;
});

$(".gear-quantity-all").on("click", function(e){
    e.preventDefault();
    var set = $(".offer-assign-table").find("form");
    var modal = $("#add-all_modal");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").html("<div class=\'alert alert-danger\'>Sprzęty powodujące konflikt lub z dodanymi sprzętami powiązanymi należy dodać ręcznie, znajdując je na liście.</div><div class=\'row\'><div class=\'col-md-4\'><h3>Prawidłowo dodane<div id=\'added-all\' class=\'label label-primary\'>0</div></h3></div><div class=\'col-md-4\'><h3>Powodujące konflikt</h3><div id=\'conflict-all\'></div></div><div class=\'col-md-4\'><h3>Ze sprzętami powiązanymi</h3><div id=\'connected-all\'></div></div></div><div style=\'text-align:right;\'><a href=\'#\' class=\'btn btn-primary \' onclick=\'location.reload();\'>Zamknij</a></div>");
        modal.modal("show");
    var length = set.length;
    set.each(function(index, element){
        var form = $(this);
        if (form.hasClass("do-not-add"))
        {
            form.closest( "tr" ).css( "background-color",  "#fff3cd");
            $("#connected-all").append(form.data("description"));
        }else{
            var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
            form.find("#gearassignment-quantity").val(val);
            var data = form.serialize();
                start = $("#warehouse_start").val();
                end = $("#warehouse_end").val();
            $.post("'.$eventGearQuantityUrl.'&start="+start+"&end="+end, data, function(success){if (success.success==1){toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
            form.closest( "tr" ).css( "background-color",  "#d4edda"); 
            ile = parseInt($("#added-all").html())+1;
            $("#added-all").html(ile);
            }else{ toastr.error(success.error);
                $("#conflict-all").append(form.data("description"));
            form.closest( "tr" ).css( "background-color",  "#f8d7da"); }
            if (index === (length - 1)) {
                  //setInterval(function(){ location.reload(); }, 1500);
              }
             });            
        }

    });
    var set = $(".offer-assign-outer-table").find("form");
    var length = set.length;
    set.each(function(index, element){
        var form = $(this);
        if (form.hasClass("do-not-add"))
        {

        }else{
            var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
            form.find("#gearassignment-quantity").val(val);
            var data = form.serialize();
            var data = {"quantity":val, "itemid":form.find("#gearassignment-itemid").val(), "add":1};

            $.post("'.$eventOuterGearQuantityUrl.'", data, function(success){if (success.success==1){toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");}else{ 
                toastr.error(success.error); 

            }
            if (index === (length - 1)) {
                  //setInterval(function(){ location.reload(); }, 1500);
              }
             });            
        }

    });
    var set = $(".offer-assign-extra-table").find("form");
    var length = set.length;
    set.each(function(index, element){
        var form = $(this);
        if (form.hasClass("do-not-add"))
        {

        }else{
            var val = parseInt(form.find("#gearassignment-quantity").val())+parseInt(form.find("#gearassignment-oldquantity").val());
            //form.find("#gearassignment-quantity").val(val);
            //var data = form.serialize();
            var data = {"quantity":val, "itemid":form.find("#gearassignment-itemid").val(), "add":1};

            $.post("'.$eventExtraQuantityUrl.'", data, function(success){if (success.success==1){toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");}else{ toastr.error(success.error); }
            if (index === (length - 1)) {
                  //setInterval(function(){ location.reload(); }, 1500);
              }
             });            
        }

    });
    return false;

});

');


$this->registerCss('

.container-working-time button { color: red; }
');

?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
        function showConnectedModal(gears, outergears){
        var modal = $("#connected_modal");
        modal.find(".modalContent").empty();
        var content = "<table class='table'><thead><tr><th>#</th><th>Nazwa</th><th>Liczba sztuk</th><th>Dostępnych</th></tr></thead><tbody>";
        var x =0;
        for (var i=0; i<gears.length; i++)
        {
            if (gears[i].in_offer==1)
            {

            }else{
                x++;
                if (gears[i].checked==1)
                    checked = "checked";
                else
                    checked = "";
                checkbox = "<td><input class=\'gear-connectedcheckbox\'  data-gearid=\'"+gears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
                content += "<tr>"+checkbox+"<td>"+gears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+gears[i].count+"'/></td><td class='connected-avability' data-gearid="+gears[i].id+"></td></tr>";                
            }

        }
        for (var i=0; i<outergears.length; i++)
        {
            if (outergears[i].in_offer==1)
            {

            }else{
                x++;
                if (outergears[i].checked==1)
                    checked = "checked";
                else
                    checked = "";
                checkbox = "<td><input class=\'gear-outerconnectedcheckbox\'  data-gearid=\'"+outergears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
                content += "<tr>"+checkbox+"<td>"+outergears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+outergears[i].count+"'/></td><td>Zewn.</td></tr>";                
            }

        }
        content += "</tbody></table>";
        content += '<div class="row"><div class="pull-right"><a class="btn btn-primary add-connected-button" href="#">Dodaj</a> ';
        content += '<a class="btn btn-default close-connected-button" href="#">Anuluj</a></div></div>';
         
        if (x>0) 
        {
            modal.find(".modalContent").append(content);
            modal.modal("show");
            $(".add-connected-button").click(function(){ saveConnected();});
            $(".close-connected-button").click(function(){  $("#connected_modal").modal("hide");});
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

        }

        function showSimilarModal(data){
            <?php if ($type=='event'){ ?>
            var modal = $("#similar_modal");
            modal.find(".modalContent").empty();
            $.post("<?=$eventGearSimilarUrl?>&packlist="+$("#select-packlist").val(), data, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
            });        
            <?php } ?>
        
        }

        function bookSimilars(){
            start = $("#warehouse_start").val();
            end = $("#warehouse_end").val();
            $.post('<?=$saveSimilarUrl?>&start='+start+'&end='+end, $("#similarForm").serialize(), function(response){
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
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
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
                        if (response.connected.length)
                        {
                            showConnectedModal(response.connected);
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
                $.post("<?=$eventGearConnectedUrl?>&start="+start+"&end="+end, data, data, function(response){
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
                                <?php if ($type=='offer'){ ?>
                                    $("body").find("[data-key='" + response.responses[i].id + "']").find("#offergear-quantity").first().val(response.responses[i].total);
                                    <?php } ?>
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
        while($date->format('Y-m-d H:i')<$event->event_end){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($event->event_end, 0, 16)."'"; ?> ];
</script>