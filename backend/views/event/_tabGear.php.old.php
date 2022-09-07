<?php

use common\models\Event;
use common\models\EventGearItem;
use common\models\Gear;
use common\models\GearItem;
use common\models\OutcomesGearOur;
use common\models\OuterGear;
use kartik\editable\Editable;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use common\components\grid\GridView;

/* @var $model \common\models\Event; */
/* @var $this \yii\web\View; */
$user = Yii::$app->user;

$not_returned_gear = null;
$notReturned = $model->getWarehouseGearDifference();
$notReturnedGear = $notReturned[0];
$notReturnedOuterGear = $notReturned[1];

$gear_our = [];
$gear_group = [];
foreach ($notReturnedGear as $gear_id => $quantity) {
    $item = GearItem::find()->where(['id' => $gear_id])->one();
    if ($item->group_id == null) {
        if ($item->name == '_ILOSC_SZTUK_') {
            $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . $quantity . "x " . $item->gear->name . "</td><td> </td><td>".Yii::t('app', 'Wewnętrzny')."</td></tr>";
        }
        else {
            $gear_our[$item->gear_id][] = $item;
        }
    }
    else {
        $gear_group[$item->gear_id][$item->group_id][] = $item;
    }
}

foreach ($gear_our as $gear_id => $items) {
    $numbers = null;
    foreach ($items as $item) {
        $numbers .= $item->number . ", ";
    }
    $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . count($items) . "x " . $items[0]->name . "</td><td>" . $numbers . "</td><td>".Yii::t('app', 'Wewnętrzny')."</td></tr>";

}

foreach ($gear_group as $gear_id => $groups ) {
    $number_list = null;
    $name = null;
    $gear_name = null;
    foreach ($groups as $group_id => $groupItems) {
        $numbers = null;
        $ids = [];
        foreach ($groupItems as $item) {
            $name = $item->name;
            $gear_name = $item->gear->name;
            $numbers .= $item->number . ", ";
            $ids[] = $item->number;
        }
        $in_order = true;
        for ($i = min($ids); $i < max($ids); $i++) {
            if (!in_array($i, $ids)) {
                $in_order = false;
            }
        }
        if ($in_order) {
            $numbers = min($ids) . "-" . max($ids) . ", ";
        }
        $number_list .= $numbers;
    }
    $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . count($groups) . "x Case " . $gear_name . " " . $name . "</td><td>" . $number_list . "</td><td>".Yii::t('app', 'Wewnętrzny')."</td></tr>";
}

foreach ($notReturnedOuterGear as $gear_id => $quantity) {
    $not_returned_gear .= "<tr><td style='white-space: nowrap;'>". $quantity . "x " . OuterGear::find()->where(['id' => $gear_id])->one()->name . "</td><td> </td><td>".Yii::t('app', 'Zewnętrzny')."</td></tr>";
}

?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęt'); ?></h3>
<div class="row">
    <div class="col-md-12">
    <div class="ibox">
            <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Packlista'), ['packing-list', 'id' => $model->id], ['class' => 'btn btn-success']);?>

        <?php

        if ($user->can('eventEventEditEyeGearManage')) {
            echo Html::a(Yii::t('app', 'Zarządzaj'), ['warehouse/assign', 'id' => $model->id, 'type' => 'event'], ['class' => 'btn btn-success']);
        }
        if ($user->can('eventRentsMagazin')) {
            echo Html::a(Yii::t('app', 'Wydaj sprzęt'), ['outcomes-warehouse/create', 'event' => $model->id], ['class' => 'btn btn-primary']);
        }
        if ($not_returned_gear) {
            echo Html::a(Yii::t('app', 'Niezwrócony sprzęt'), '#tab-gear', ['class'=>'btn btn-danger', 'id' => 'display-not-returned-gear']);
        }
        if ($user->can('eventRentsMagazin')) {
            echo Html::a(Yii::t('app', 'Przyjmij sprzęt'), ['incomes-warehouse/create', 'event' => $model->id], ['class' => 'btn btn-primary']);
        }
        if ($user->can('gearWarehouseOutcomesView')) {
            foreach ($model->outcomesForEvents as $outcome) {
                echo ' ' . Html::a(Yii::t('app', 'Wydanie') . ' nr: ' . $outcome->id, ['outcomes-warehouse/view', 'id' => $outcome->id], ['class' => 'btn btn-warning', 'target' => '_blank']);
            }
        }
        if ($user->can('gearWarehouseIncomesView')) {
            foreach ($model->incomesForEvents as $income) {
                echo ' ' . Html::a(Yii::t('app', 'Zwrot') . ' nr: ' . $income->id, ['incomes-warehouse/view', 'id' => $income->id], ['class' => 'btn btn-danger', 'target' => '_blank']);
            }
        } ?>
            </div>
    </div>
</div>

<table class="table table-stripped btn-danger" style="width: 50%; margin: auto; display: none;" id="not-returned-table">
    <tr><td><strong><?= Yii::t('app', 'Niezwrócony sprzęt') ?>:</strong></td><td><strong><?= Yii::t('app', 'Numery') ?>:</strong></td><td><strong><?= Yii::t('app', 'Magazyn') ?></strong></td></tr>
    <?= $not_returned_gear ?>
</table>

<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php

            $planned = ['rowOptions'=>[], 'quantity' => [], 'delete' => [], 'gear_numbers' => [], 'case_number' => [], 'after_row' => []];
            $quantities = Event::getAssignedQuantities($model->id);
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedGearModel(),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'rowOptions' => function ($gear) use ($model, &$planned) {
                    // planowany sprzęt
                    if (!in_array($gear->id, $planned['rowOptions'])) {
                        $planned['rowOptions'][] = $gear->id;
                        $planned_gears = [];
                        $gear_out_no = 0;
                        $gear_no = 0;

                        /** @var \common\models\Gear $gear */
                        foreach ($gear->gearItems as $gear_item) {
                            if (EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_id' => $gear_item->id])) {
                                $planned_gears[] = $gear_item;
                                $gear_no++;
                            }
                        }

                        if ($gear_no == 0) {
                            return ['class' => 'row-all-gear-out-unplanned'];
                        }

                        $outcomes = $model->outcomesForEvents;
                        foreach ($outcomes as $outcome) {
                            foreach ($planned_gears as $planned_gear) {
                                $outcomesGearOur = OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->andWhere(['gear_id' => $planned_gear->id])->all();
                                foreach ($outcomesGearOur as $outcomeGearOur) {
                                    $gear_out_no += $outcomeGearOur->gear_quantity;
                                }
                            }
                        }

                        if ($gear_out_no == $gear_no) {
                            return ['class' => 'row-all-gear-out-planned'];
                        }
                        else {
                            if ($gear_out_no != 0) {
                                return ['class' => 'row-not-all-gear-out-planned'];
                            }
                        }
                    }
                    // nieplanowany sprzęt
                    else {
                        return['class' => 'row-all-gear-out-unplanned'];
                    }
                    return null;
                },
                'afterRow' => function($gearModel) use ($model, &$planned) {
                    if (!in_array($gearModel->id, $planned['after_row'])) {
                        $planned['after_row'][] = $gearModel->id;
                        $planned_value = 1;

                        $plann = false;
                        foreach ($model->getGearItems()->where(['gear_id' => $gearModel->id])->all() as $gear) {
                            $event_gear =  EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear->id])->one();
                            if ($event_gear && $event_gear->planned == 1) {
                                $plann = true;
                            }
                        }
                        if (!$plann) {
                            $planned_value = 0;
                        }
                    }
                    else {
                        $planned_value = 0;
                    }
                    $gear = [];
                    $gear_no = $model->getGearItems()->where(['gear_id' => $gearModel->id])->all();
                    foreach ($gear_no as $gear_item) {
                        $gearEvent = EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear_item->id])->andWhere(['planned'=>$planned_value])->one();
                        if ($gearEvent) {
                            $gear[] = $gear_item;
                        }
                    }

                    $not_case = [];
                    $case = [];
                    foreach ($gear as $gearItem) {
                        if ($gearItem->group_id == null) {
                            $not_case[] = $gearItem;
                        }
                        else {
                            $case[$gearItem->group_id][] = $gearItem;
                        }
                    }

                    $event_start = $model->getTimeStart();
                    $event_end = $model->getTimeEnd();
                    $case_table = null;

                    $workingTimeText = null;
                    if ($planned_value) {
                        $workingTimeText = Yii::t('app', 'Czas pracy');
                    }

                    $display = 'none';
                    if (isset($_GET['model']) && $_GET['model'] == $gearModel->id) {
                        $display = "table";
                    }

                    $case_table = '<table class="table table-bordered" data-rows="'.count($case).'" style="display: '.$display.'; width: 80%; margin: auto;">';
                    $case_table .= '<tr>
                                        <td></td>
                                        <td><strong>'.Yii::t('app', 'Numer QR/Bar').'</strong></td>
                                        <td><strong>'.Yii::t('app', 'Numery urządzeń').'</strong></td>
                                        <td><strong>'.$workingTimeText.'</strong></td>
                                        <td></td>
                                    </tr>';
                    foreach ($case as $case_id => $items) {
                        $tr_class = null;

                        $item_count = 0;
                        foreach ($items as $item) {
                            foreach ($model->outcomesForEvents as $outcome) {
                                if (OutcomesGearOur::find()->where(['outcome_id'=>$outcome->id])->andWhere(['gear_id'=>$item->id])->count() == 1) {
                                    $item_count++;
                                }
                            }
                        }
                        if ($item_count == count($items)) {
                            $tr_class = 'row-all-gear-out-planned';
                        }

                        $gearGroup = \common\models\GearGroup::find()->where(['id' => $items[0]->group_id])->one();
                        $case_table .= "<tr class='".$tr_class."'><td>" . Html::img('/img/case.jpg', ['style' => 'width:100px;height:100px;']). "</td>";
                        $case_table .= "<td>".$gearGroup->getBarCodeValue()."</td>";
                        $case_table .= "<td>";
                        $countItems = 0;
                        foreach ($items as $item) {
                            $case_table .= $item->number . ", ";
                            $countItems++;
                        }
	                    foreach ($items as $item) {
		                    if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
			                    $case_table .= "<br>".Html::tag('span', Yii::t('app', 'W serwisie numer').': '.$item->number, ['class' => 'label label-danger']);
		                    }
	                    }
                        $eventGearItem = EventGearItem::find()->where(['event_id'=>$model->id])->andWhere(['gear_item_id'=>$items[0]->id])->one();
                        $displayValue = Yii::t('app', 'Cały event');
                        if ($eventGearItem->start_time != $event_start || $eventGearItem->end_time != $event_end) {
                            $displayValue = $eventGearItem->start_time . " - " . $eventGearItem->end_time;
                        }
                        $widget = null;
                        $editButtons = null;
                        if ($planned_value) {
                            if (Yii::$app->user->can('eventEventEditEyeGearEdit')){
                                $widget = Editable::widget([
                                    'formOptions' => [
                                        'action'=>['event/update-working-time-event-gear-item', 'eventId'=>$model->id, 'gearId' => $items[0]->gear_id, 'gearGroup'=>$items[0]->group_id],
                                    ],
                                    'asPopover' => true,
                                    'placement' => PopoverX::ALIGN_LEFT,
                                    'inputType' => Editable::INPUT_DATE_RANGE,
                                    'header' => Yii::t('app', 'Czas pracy'),
                                    'size' => PopoverX::SIZE_LARGE,
                                    'model' => $eventGearItem,
                                    'attribute' => 'dateRange',
                                    'displayValue' => $displayValue,
                                    'submitButton'=>[
                                        'icon' => Html::icon('ok'),
                                        'class'=>'btn btn-sm btn-primary'
                                    ],
                                    'options'=> [

                                        'id'=>'edit-'.$items[0]->id.'-'.$model->id,
                                        'options'=>[
                                            'style'=>'width: 100%',
                                            'id'=>'picker-'.$items[0]->id.'-'.$model->id,
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
                                        "editableSuccess"=>"function(event, val, form, data) { 
                                            var url = window.location.href;    
                                            if (url.indexOf('?') > -1){
                                               url += '&model=' + data.gear_id;
                                            }else{
                                               url += '?model=' + data.gear_id;
                                            }
                                            window.location.href = '/admin/event/view?id=".$model->id."&model=' + data.gear_id + '#tab-gear';  
                                            location.reload();
                                        }",
                                    ],
                                ]);
                            }
                            else {
                                $widget = $displayValue;
                            }
                            $editButtons = Html::a(Html::icon('pencil'), ['gear-group/update', 'id' => $case_id]);
                            if(Yii::$app->user->can('eventEventEditEyeGearDelete')) {
                                $editButtons .=
                                    Html::a(Html::icon('remove'),
                                        [
                                            '/warehouse/unassign-gear-group',
                                            'event_id' => $model->id,
                                            'case_id' => $case_id,
                                            //'type'=>$model->getClassType()
                                        ],
                                        [
                                            'data' => [
                                                'itemsnumber' => $countItems,
                                            ],
                                            'class' => 'remove-assignment-group',
                                        ]

                                    );
                            }
                        }
                        $case_table .= "</td><td>".$widget."</td>";
                        $case_table .= "<td>" . $editButtons . "</td></tr>";
                    }

                    foreach ($not_case as $item) {
                        $eventGearItem = EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $item->id])->one();
                        if ($eventGearItem->start_time == $event_start && $eventGearItem->end_time == $event_end) {
                            $display_value = Yii::t('app', 'Cały event');
                        }
                        else {
                            $display_value = $eventGearItem->start_time . " - " . $eventGearItem->end_time;
                        }

                        $edit_time = null;
                        $remove_link = null;
                        if ($planned_value) {
                            if (Yii::$app->user->can('eventEventEditEyeGearEdit')){
                                $edit_time =  Editable::widget([
                                    'formOptions' => [
                                        'action' => [
                                            'event/update-working-time-event-gear-item',
                                            'eventId' => $model->id,
                                            'gearId' => $item->gear_id,
                                            'itemId' => $item->id
                                        ],
                                    ],
                                    'asPopover' => true,
                                    'placement' => PopoverX::ALIGN_LEFT,
                                    'inputType' => Editable::INPUT_DATE_RANGE,
                                    'header' => 'Czas pracy',
                                    'size' => PopoverX::SIZE_LARGE,
                                    'model' => $eventGearItem,
                                    'attribute' => 'dateRange',
                                    'displayValue' => $display_value,
                                    'submitButton' => [
                                        'icon' => Html::icon('ok'),
                                        'class' => 'btn btn-sm btn-primary'
                                    ],
                                    'containerOptions' => [
                                        'style' => 'display: inline-block;'
                                    ],
                                    'options' => [
                                        'id' => 'edit-' . $item->id . '-' . $model->id,
                                        'options' => [
                                            'style' => 'width: 100%',
                                            'id' => 'picker-' . $item->id . '-' . $model->id,
                                            'class' => 'form-controll',
                                        ],
                                        'convertFormat' => true,
                                        'startAttribute' => 'start_time',
                                        'endAttribute' => 'end_time',
                                        'pluginOptions' => [
                                            'timePicker' => true,
                                            'timePickerIncrement' => 5,
                                            'timePicker24Hour' => true,
                                            'locale' => ['format' => 'Y-m-d H:i:s'],
                                        ],
                                    ],
                                    'pluginEvents' => [
                                        "editableSuccess"=>"function(event, val, form, data) { 
                                            window.location.href = '/admin/event/view?id=".$model->id."&model=' + data.gear_id + '#tab-gear';  
                                            location.reload();
                                        }",
                                    ],
                                ]);
                            }
                            else {
                                $edit_time = $display_value;
                            }
                            if (Yii::$app->user->can('eventEventEditEyeGearDelete')) {
                                $remove_link = Html::a(Html::icon('remove'), ['/warehouse/assign-gear',
                                    'id' => $model->id,
                                    'type' => $model->getClassType()], ['data' => ['itemId' => $item->id, 'add' => 0,
                                    'name' => $item->name], 'class' => 'remove-assignment-button']);
                            }
                        }

                        $tr_class = null;
                        foreach ($model->outcomesForEvents as $outcome) {
                            if (OutcomesGearOur::find()->where(['outcome_id' => $outcome->id])->andWhere(['gear_id' => $item->id])->count() == 1) {
                                $tr_class = 'row-all-gear-out-planned';
                            }
                        }

                        $case_table .= "<tr class='".$tr_class."'>";
                        $case_table .= "<td></td>";
                        $case_table .= "<td>".$item->getBarCodeValue()."</td>";
                        $case_table .= "<td>".$item->number."</td>";
                        $case_table .= "<td>".$edit_time."</td>";
                        $case_table .= "<td>" . $remove_link . "</td>";

                        $case_table .= "</tr>";
                    }

                    $case_table .= '</table>';

                    return "<tr class='sub_models'><td colspan='7'>".$case_table."</td></tr>";
                },
                'columns' => [
                    [
                        'label' => '#',
                        'format' => 'html',
                        'value' => function($gear, $number) use ($model) {
                            return $number+1 . Html::a(Icon::show('arrow-down', [], Icon::BSG), ['#'], ['class' => 'showGroup']);
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Zdjęcie'),
                        'format' => 'html',
                        'value' => function ($model) {
                            /* @var $model \common\models\Gear */
                            if ($model->photo == null) {
                                return "-";
                            }
                            else {
                                return Html::a(Html::img($model->getPhotoUrl(), ['width'=>100]), ['gear/view', 'id'=>$model->id]);
                            }
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Model'),
                        'format' => 'html',
                        'value' => function($model) {
                            return Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Sprzęt'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $category_name = $model->category->name;
                            $categories = $model->category->parents()->all();
                            if (count($categories) > 1) {
                                $category_name = $categories[1]->name;
                            }
                            return $category_name;
                        },
                        'group'=>true,
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'grouped-category-row',
                        'groupEvenCssClass'=>'grouped-category-row',
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'html',
                        'value' => function($gear) use ($model, &$planned) {
                            if (!in_array($gear->id, $planned['quantity'])) {
                                $planned['quantity'][] = $gear->id;
                                $planned_value = 1;
                            }
                            else {
                                $planned_value = 0;
                            }

                            $planned_gears = [];
                            $gear_no = 0;

                            /** @var \common\models\Gear $gear */
                            foreach ($gear->gearItems as $gear_item) {
                                if (EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_id' => $gear_item->id])) {
                                    $planned_gears[] = $gear_item;
                                    $gear_no++;
                                }
                            }
                            if ($gear_no == 0 ) {
                                $planned_value = 0;
                            }

                            $gear_no = $model->getGearItems()->where(['gear_id' => $gear->id])->all();
                            $gear_number = 0;
                            foreach ($gear_no as $gear) {
                                $gearEvent = EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear->id])->andWhere(['planned'=>$planned_value])->one();
                                if ($gearEvent && $gearEvent->quantity > 0) {
                                    $gear_number += $gearEvent->quantity;
                                }
                                else if ($gearEvent) {
                                    $gear_number++;
                                }
                            }

                            $gearModel = Gear::findOne($gear->gear_id);
	                        if (!$gearModel->no_items) {
		                        $serwisNumber = 0;
		                        foreach ($gearModel->gearItems as $item) {
			                        if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
				                        $serwisNumber++;
			                        }
		                        }

		                        $serwis = null;
		                        if ($serwisNumber > 0) {
			                        $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
		                        }

		                        $gear_number .= " " . $serwis;
	                        }

                            return $gear_number;
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Numery'),
                        'format' => 'html',
                        'value' => function($gear) use ($model, &$planned) {
                            if (!in_array($gear->id, $planned['case_number'])) {
                                $planned['case_number'][] = $gear->id;
                                $planned_value = 1;

                                $plann = false;
                                foreach ($model->getGearItems()->where(['gear_id' => $gear->id])->all() as $gear_model) {
                                    $event_gear =  EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear_model->id])->one();
                                    if ($event_gear && $event_gear->planned == 1) {
                                        $plann = true;
                                    }
                                }
                                if (!$plann) {
                                    $planned_value = 0;
                                }
                            }
                            else {
                                $planned_value = 0;
                            }
                            $event_start = $model->getTimeStart();
                            $event_end = $model->getTimeEnd();


                            $result = null;
                            $groups = [];

                            /** @var \common\models\Gear $gear */
                            foreach ($gear->gearItems as $gear_item) {
                                $workingTimeClass = null;

                                /** @var \common\models\EventGearItem $planned_gear */
                                if ($planned_gear = EventGearItem::find()->where(['event_id' => $model->id])->andWhere(['gear_item_id' => $gear_item->id])->one()) {
                                    foreach ($model->outcomesForEvents as $outcome) {
                                        if (OutcomesGearOur::find()->where(['outcome_id' => $outcome->id])->andWhere(['gear_id' => $planned_gear->gear_item_id])->count() == 1 && $planned_value) {
                                            $workingTimeClass = 'row-all-gear-out-planned';
                                        }
                                    }
                                    if (($planned_gear->start_time != $event_start || $planned_gear->end_time != $event_end) && $planned_value) {
                                        if ($workingTimeClass == null) {
                                            $workingTimeClass = 'yellow-background';
                                        }
                                        else {
                                            $workingTimeClass .= ' yellow-border';
                                        }
                                    }
                                    $gear_item = GearItem::find()->where(['id' => $planned_gear->gear_item_id])->andWhere(['gear_id' => $gear->id])->one();
                                    if ($gear_item && $gear_item->group_id != null) {
                                        $groups[$gear_item->group_id][] = [$gear_item, $workingTimeClass];
                                    }
                                    if ($gear_item && $gear_item->group_id == null) {
                                        $result .= "<span class='" . $workingTimeClass . "'>" . $gear_item->number . "</span>, ";
                                    }
                                }
                            }

                            foreach ($groups as $group_id => $items) {
                                $css_class = null;
                                if ($planned_value) {
                                    $item_count = 0;
                                    foreach ($items as $item_arr) {
                                        foreach ($model->outcomesForEvents as $outcome) {
                                            if (OutcomesGearOur::find()->where(['outcome_id'=>$outcome->id])->andWhere(['gear_id'=>$item_arr[0]->id])->count() == 1) {
                                                $item_count++;
                                            }
                                        }
                                    }
                                    if ($item_count == count($items) && $planned_value) {
                                        $css_class = 'row-all-gear-out-planned';

                                        $gear_planned = EventGearItem::find()->where(['event_id'=>$model->id])->andWhere(['gear_item_id'=>$items[0][0]->id])->one();
                                        if (($gear_planned->start_time != $event_start || $gear_planned->end_time != $event_end) && $planned_value ) {
                                            $css_class .= ' yellow-border';
                                        }
                                    }

                                }
                                if ($css_class == null) {
                                    $css_class = $items[0][1];
                                }
                                $result .= '<span class="'.$css_class.'">[';

                                $numer_list = null;
                                $ids = [];
                                foreach ($items as $item_arr) {
                                    $numer_list .= $item_arr[0]->number . ", ";
                                    $ids[] = $item_arr[0]->number;
                                }
                                $in_order = true;
                                for ($i = min($ids); $i < max($ids); $i++) {
                                    if (!in_array($i, $ids)) {
                                        $in_order = false;
                                    }
                                }
                                if ($in_order) {
                                    $numer_list = min($ids) . "-" . max($ids);
                                }

                                $result .= $numer_list . ']</span>, ';
                            }

                            return $result;
                        }
                    ],
                ],
            ]);

            $this->registerJs('
                $("#display-not-returned-gear").click(function(e){
                    e.preventDefault();
                    $("#not-returned-table").toggle();
                });
            
                $(".remove-assignment-button").on("click", function(e){
                    e.preventDefault();
                    
                    var quantityElement = $(this).parent().parent().parent().parent().parent().parent().prev().find(":nth-child(4)");
                    var quantity = parseInt(quantityElement.html());
                    quantityElement.html(quantity-1); 
                    $(this).parent().parent().remove();
                    if (quantity == 1 || $(this).data("name") == "_ILOSC_SZTUK_") {
                        if (quantityElement.parent().next().hasClass("sub_models")) {
                            quantityElement.parent().next().remove();
                        }
                        quantityElement.parent().prev().remove();
                        quantityElement.parent().remove();
                    }
                        
                    var $el = $(this);
                    $.post($el.prop("href"), $el.data(), function(response) {
                        if (response.success == 1)
                        {
                            $(".item-" + $el.data(\'itemid\')).each(function(){
                                $(this).remove();
                            });
                        }
                    });
                    return false;
                });
                
                $(".remove-assignment-group").click(function(e){
                    e.preventDefault();
                    
                    var quantity = $(this).data("itemsnumber");
                    var table = $(this).parent().parent().parent().parent();
                    var parentTable = table.parent().parent().prev();
                    var items = parentTable.children("td").eq(3);
                    var itemsNo = parseInt(items.html());
                    
                    $(this).parent().parent().remove();
                    items.html(itemsNo-quantity);

                    if ((itemsNo-quantity) <= 0) {
                        parentTable.next().remove();
                        parentTable.remove();
                    }
                    
                    $.post($(this).prop("href"), function(response) {
                        if (response.success == 1) {
                            console.log(response.success + "567");
                            $(this).parent().parent().remove();
                        }
                        else {
                            console.log(response.success + "453");
                        }
                    });
                });
                
                var counter = 0;
                $(".showGroup").click(function(e){
                    e.preventDefault();
                    counter++;
                    
                    if (counter % 2 == 0) {
                        $(this).find("i").attr("class", "glyphicon glyphicon-arrow-down");
                        $(this).parent().parent().next().find(":first-child").find(":first-child").slideUp();
                    }
                    else {
                        $(this).find("i").attr("class", "glyphicon glyphicon-arrow-up");
                        $(this).parent().parent().next().find(":first-child").find(":first-child").slideDown();
                    }
                });
                
                $(".table-bordered").each(function(){
                    $(this).removeClass("table-bordered");
                });
                $(".table-striped").each(function(){
                    $(this).removeClass("table-striped");
                });
            ');
        ?>
    </div>
</div>
    </div>
</div>
</div>

<?php

$this->registerCss('

.kv-editable {
    display: block;
}

.row-all-gear-out-planned {
    background-color: #449D44;
    color: white;
}
.row-not-all-gear-out-planned {
   /* background-color: yellow; */
}
.row-all-gear-out-unplanned {
    background-color: gray;
    color: white;
}
.row-all-gear-out-planned a, 
.row-all-gear-out-planned button,
.row-all-gear-out-unplanned a, 
.row-all-gear-out-unplanned span {
    color: white;
}

.working-time-link {
    border-bottom: 1px dashed #428bca;
    color: #428bca;
    cursor: pointer;
}
.working-time-link:hover {
    color: #286090;
    border-bottom-color: #286090;
}
.grouped-category-row {
    background-color: orangered;
    color: white;
}

.table > tbody > tr > td.grouped-category-row {
    padding: 0;
}
.remove-assignment-button {
    margin-top: 5px;
    display:block;
}
.kv-editable-loading {
    display: none !important;
}

.yellow-background {
    background-color: yellow;
}

.yellow-border {
    border: 3px solid #aa21ee;
}

');