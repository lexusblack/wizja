<?php
use common\helpers\ArrayHelper;

use common\models\Event;
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearItem;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OuterGear;
use kartik\editable\Editable;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
$user = Yii::$app->user;
$checkGearConflictsUrl = Url::to(['warehouse/gear-conflicts', 'event_id'=>$model->id]);
$eventGearConflictsUrl = Url::to(['warehouse/gear-conflicts-modal', 'event_id'=>$model->id]);
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <p>
                <?php 
                $dropList = [];
                $dropList["change_time"] = Yii::t('app', 'Zaznaczonym zmień czas rezerwacji ');
                $dropList["delete"] = Yii::t('app', 'Usuń zaznaczone');
                foreach ($model->packlists as $p)
                {
                    $dropList["packlist_".$p->id] = Yii::t('app', 'Zaznaczone dodaj do ').$p->name;
                }
                $pac = new \common\models\form\GearActionForm();
                $form = ActiveForm::begin(['id' => 'action-form', 'type'=>ActiveForm::TYPE_INLINE,]);

                echo   $form->field($pac, 'items')->hiddenInput()->label(false);

                echo $form->field($pac, 'action')->dropDownList($dropList)->label(Yii::t('app', "Z zaznaczonymi"));

                echo Html::submitButton(Yii::t('app', 'Wykonaj') , ['class' => 'btn btn-success action-form-submit']);

                ActiveForm::end();
         ?>
            </p>
        <?php
            $planned = ['rowOptions'=>[], 'quantity' => [], 'delete' => [], 'gear_numbers' => [], 'case_number' => [], 'after_row' => []];
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedGearModel(),
                'id'=>'allGearTable',
                'tableOptions' => [

                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_id = $categories[1]->id;
                            }else{
                                $category_id = $category->id;
                            }
                    return [
                        'data' => ['key' => $model->id, 'main-category'=>$category_id, 'sub-category'=>$category->id],
                    ];
                },
                'afterRow' => function($gear) use ($model, &$planned) {
                    $gearModel = $gear->gear;
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

                    return "<tr class='sub_models'><td colspan='7' style='padding:0; border:0;'>".$case_table."</td></tr>";
                },
                'columns' => [
                [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true, 
                'checkboxOptions' => function($model) {
                    return ['value' => $model->gear_id];
                },
                ],
                ['attribute' => 'id', 'visible' => false],
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
                            if ($model->gear->photo == null) {
                                return "-";
                            }
                            else {
                                return Html::a(Html::img($model->gear->getPhotoUrl(), ['width'=>50]), ['gear/view', 'id'=>$model->gear->id]);
                            }
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Model'),
                        'format' => 'html',
                        'value' => function($model) {
                            return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear->id]);
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'raw',
                        'attribute'=>'quantity',
                        'value' => function($model) use ($user)
                        {
                            if (($user->can('eventEventEditEyeGearManage'))&&(((!$model->event->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))) {
                                $content = '';
                                    $this->beginBlock('form');

                                    $assignmentForm = new \common\models\form\GearAssignment();
                                    $assignmentForm->itemId = $model->gear_id;
                                    $assignmentForm->quantity = $model->quantity;
                                    $assignmentForm->oldQuantity = $assignmentForm->quantity;
                                    $isAvailable = true;

                                    $form = ActiveForm::begin([
                                        'options' => [
                                            'class'=>'gear-assignment-form',
                                        ],
                                        'action' =>['assign-gear', 'id'=>$model->event->id, 'type'=>'event'],
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
                                return $content;                                   
                            }else{
                                return $model->quantity;
                            }
                            
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Pakowany'),
                        'format'=>'raw',
                        'value'=> function($model){
                            $result = "";
                            foreach ($model->gear->getPacking() as $case){
                                $result .= $case."</br>";
                            }
                            return $result;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'format'=>'raw',
                        'value' =>function($gear) use ($model) {
                            $event_start = $model->getTimeStart();
                            $event_end = $model->getTimeEnd();
                            if ($gear->start_time == $event_start && $gear->end_time == $event_end) {
                                $display_value = Yii::t('app', 'Cały event');
                            }
                            else {
                                $display_value = $gear->start_time . " - " . $gear->end_time;
                            }
                            if ((Yii::$app->user->can('eventEventEditEyeGearEdit'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))){
                                $widget = Editable::widget([
                                    'formOptions' => [
                                        'action'=>['event/update-working-time-event-gear', 'eventId'=>$model->id, 'gearId' => $gear->gear_id,
                                    ]],
                                    'asPopover' => true,
                                    'placement' => PopoverX::ALIGN_LEFT,
                                    'inputType' => Editable::INPUT_DATE_RANGE,
                                    'header' => Yii::t('app', 'Czas pracy'),
                                    'size' => PopoverX::SIZE_LARGE,
                                    'model' => $gear,
                                    'attribute' => 'dateRange',
                                    'displayValue' => $display_value,
                                    'submitButton'=>[
                                        'icon' => Html::icon('ok'),
                                        'class'=>'btn btn-sm btn-primary'
                                    ],
                                    'options'=> [

                                        'id'=>'edit-'.$gear->gear_id.'-'.$model->id,
                                        'options'=>[
                                            'style'=>'width: 100%',
                                            'id'=>'picker-'.$gear->gear_id.'-'.$model->id,
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
                                $widget = $display_value;
                            }
                            return $widget;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Sprzęt'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_name = $categories[1]->name;
                                $category =  $categories[1];
                            }else{
                                $category_name = $category->name;
                            }
                            return '<input type="checkbox" data-category='.$category->id.' class="category-chackbox"> '.$category_name;
                        },
                        'group'=>true,
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'grouped-category-row',
                        'groupEvenCssClass'=>'grouped-category-row',
                    ],
                    [
                        'label' => Yii::t('app', 'Sprzęt 2'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $category = $model->gear->category;
                            return '<input type="checkbox" data-category='.$category->id.' class="category-chackbox"> '.$category->name;
                        },
                        'group'=>true,
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'grouped-category-row2',
                        'groupEvenCssClass'=>'grouped-category-row2',
                    ],
                    [
                        'label' => Yii::t('app', 'Packlisty'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $total = 0;
                            $content = "";
                            foreach ($model->packlistGears as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                            }
                            $not = $model->quantity - $total;
                            if ($not)
                                $content .='<span class="label label-warning" style="background-color:#222">'.$not.'</span> ';
                            return $content;
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Komentarz'),
                        'attribute'=>'comment',
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'comment',
                            'inputType' => Editable::INPUT_TEXT,
                                'formOptions' => [
                                        'action'=>['/event/save-gear-comment', 'gear_id'=>$gear->id],
                                    ],
                                'options' => [
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Objętność'),
                        'value' => function($eg) use ($model){
                                    $sum = 0;
                                    if ($eg->gear->no_items)
                                    {
                                        $sum =$eg->quantity*$eg->gear->volume;
                                    }else{
                                        $items = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$eg->gear_id])->all(), 'id', 'id');
                                        $eventGearItems = EventGearItem::find()->where(['event_id'=>$model->id])->andWhere(['IN', 'gear_item_id', $items])->count();  
                                        $count =  $eg->quantity - $eventGearItems;
                                        if ($eventGearItems>0)
                                        {
                                            //liczymy dodane egzemplarze i case
                                            $ids = ArrayHelper::getColumn($model->getGearItems()->where(['IN', 'id', $items])->all(), 'group_id');
                                            $cases = \common\models\GearGroup::find()->where(['IN', 'id', $ids])->all();
                                            $gearNoCase = $model->getGearItems()->where(['group_id'=>null])->andWhere(['IN', 'id', $items])->all();
                                            $volumeCase = array_sum(ArrayHelper::getColumn($cases, 'calculatedVolume'));
                                            $volumeNoCase = array_sum(ArrayHelper::getColumn($gearNoCase, 'calculatedVolume'));
                                            $sum+=$volumeCase+$volumeNoCase;

                                        }
                                        if ($count>0)
                                        {
                                            //jesli sprzęt został dodany ilościowo to liczymy tak mniej więcej
                                            $sum =$eg->gear->countVolume2($count);
                                        }

                                    }
                                    return Yii::$app->formatter->asDecimal($sum,2);
                        }
                    ],
                    [
                        'format' =>'raw',
                        'value' => function ($gear) use ($model){
                            $remove_link = "";
                            if ((Yii::$app->user->can('eventEventEditEyeGearDelete'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))) {
                                $remove_link = Html::a(Html::icon('remove'), ['/warehouse/remove-gear',
                                    'id' => $model->id,
                                    'type' => $model->getClassType(),
                                    'noItem'=>1],
                                     ['data' => ['itemId' => $gear->gear_id,
                                    'name' => $gear->gear->name], 'class' => 'remove-assignment-button']);
                            }
                            return $remove_link;
                        }
                    ],
                    ]
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
                            $.post("'.$checkGearConflictsUrl.'&gear_id="+$el.data(\'itemid\'), $el.data(), function(response){
                            if (parseInt(response.conflicts)>0)
                            {
                                showConflictsToResolveModal($el.data(\'itemid\'), quantity);
                            }
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
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h5><?php echo Yii::t('app', 'Lista sprzętu zewnętrznego'); ?></h5>
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedOuterGears(),
                'id'=>'orderGear2',
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                    [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true,
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ["value" => $model->name];
                },
                ],
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    [
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            /* @var $model \common\models\OuterGear */
                            if ($model->getPhotoUrl() == null)
                            {
                                return '-';
                            }
                            return Html::a(Html::img($model->getPhotoUrl(), ['width'=>50]), ['outer-gear-model/view', 'id'=>$model->outer_gear_model_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'outer_gear_id',
                        'label'=>Yii::t('app', 'Nazwa'),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a($model->getName(), ['outer-gear-model/view', 'id'=>$model->outer_gear_model_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            return $gear_no->quantity;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Zamówienie'),
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->order_id)
                                return Html::a(Yii::t('app', 'Zamówienie nr').' '.$gear_no->order_id, ['/order/view', 'id' => $gear_no->order_id]);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'class'=>\common\components\grid\WorkingTimeColumn::className(),
                        'parentModel' => $model,
                        'type'=>'outer_gear',
                        'connectionClassName' =>\common\models\EventOuterGear::className(),
                        'itemIdAttribute'=>'outer_gear_id',
                        'visible' => Yii::$app->user->can('eventEventEditEyeOuterGearEdit'),
                    ],
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'value' => function ($gear) use ($model) {
                            $EventOuterGear = EventOuterGear::find()->where(['event_id' => $model->id])->andWhere(['outer_gear_id' => $gear->id])->one();
                            return $EventOuterGear->start_time . " - " . $EventOuterGear->end_time;
                        },
                        'visible' => !Yii::$app->user->can('eventEventEditEyeOuterGearEdit')
                    ],
                    [
                        'value'=>'company.displayLabel',
                        'attribute' => 'company_id',
                    ],
                    [
                        'label' => Yii::t('app', 'Data odbioru'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'reception_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'event_id'=>$model->id],
                                    ],
                                'options' => [
                                'pluginOptions' => [
                                     'format' => 'yyyy-mm-dd'
                                     ]
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->reception_time)
                                return substr($gear_no->reception_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Data zwrotu'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'return_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'event_id'=>$model->id],
                                    ],
                                'options' => [
                                'pluginOptions' => [
                                     'format' => 'yyyy-mm-dd'
                                     ]
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->return_time)
                                return substr($gear_no->return_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Uwagi'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'description',
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'event_id'=>$model->id],
                                    ],
                                'options' => [

                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->description)
                                return $gear_no->description;
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Odpowiedzialny'),
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'editableOptions' => function ($gear, $key, $index) use ($model) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2, 
                            'name'=>'user_id',
                            'formOptions' => [
                                    'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'event_id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\User::getList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ],
                        'pluginEvents' =>   [ 
                            "editableSuccess"=>"function(event, val, form, data) { }",
                        ]
                        ];
                    },
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->user_id)
                                return $gear_no->user->displayLabel;
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Packlisty'),
                        'format' => 'html',
                        'value' => function($gear) use ($model) {
                            $total = 0;
                            $content = "";
                            $gear_no = $model->getEventOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            $pack = \common\models\PacklistOuterGear::find()->joinWith(['packlist'])->where(['event_outer_gear'=>$gear->id])->andWhere(['packlist.event_id'=>$model->id])->all();
                            foreach ($pack as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                            }
                            $not = $gear_no->quantity - $total;
                            if ($not)
                                $content .='<span class="label label-warning" style="background-color:#222">'.$not.'</span> ';
                            return $content;
                        },
                    ],


                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{remove-assignment}',
                        'controllerId'=>'outer-warehouse',
                        'buttons' => [
                            'remove-assignment' => function ($url, $item, $key) use ($model) {
                                $button = '';
                                if (Yii::$app->user->can('eventEventEditEyeOuterGearDelete'))
                                {

                                    $button =  Html::a(Html::icon('remove'), ['/outer-warehouse/assign-o-gear', 'id'=>$model->id, 'type'=>$model->getClassType()], [
                                        'data'=> [
                                            'itemId'=>$item->id,
                                            'add'=>0,
                                            'quantity' => 0
                                        ],
                                        'class'=>'remove-assignment-button-outer'
                                    ]);
                                }
                                return $button;
                            }
                        ]
                    ]
                ],
            ])
        ?>
    </div>
</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">

        <h5><?php echo Yii::t('app', 'Lista sprzętu dodatkowego'); ?></h5>
        <?php if ($user->can('eventEventEditEyeGearManage')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['event-extra-item/create', 'event_id' => $model->id], ['class' => 'btn btn-success add-extra-item']);
        } ?>
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedExtraItems(),
                'id'=>'extraItem',
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true,
                ],
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                            ['attribute' => 'id', 'visible' => false],
        'name',
        'quantity',
        [
                'attribute' => 'gear_category_id',
                'label' => 'Sekcja',
                'value' => function($model){
                    if ($model->gearCategory)
                    {return $model->gearCategory->name;}
                    else
                    {return NULL;}
                },
        ],
        [
            'attribute'=>'weight',
            'value'=>function($model)
            {
                return $model->quantity*$model->weight;
            }
        ],
        [
            'attribute'=>'volume',
            'value'=>function($model)
            {
                return $model->quantity*$model->volume;
            }
        ],
                    [
                        'label' => Yii::t('app', 'Packlisty'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $total = 0;
                            $content = "";
                            foreach ($model->packlistGears as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                            }
                            $not = $model->quantity - $total;
                            if ($not)
                                $content .='<span class="label label-warning" style="background-color:#222">'.$not.'</span> ';
                            return $content;
                        },
                    ],
        [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{update}{delete}',
                        'controllerId'=>'event-extra-item',
        ],
                ],
            ])
        ?>
    </div>
</div>
    </div>
</div>


<?php 
$modalPacklistUrl = Url::to(['event/get-packlist-modal', 'id'=>$model->id]);
$groupDeleteUrl = Url::to(['warehouse/gear-group-delete', 'id'=>$model->id]);

$this->registerJs('
$("#action-form").on("beforeSubmit", function(e){
    //robimy tutaj serializację zaznaczonych checkboxów w trzech tabelkach i wyświetlamy opcję do wpisania ilości
    var gears = $("#allGearTable").yiiGridView("getSelectedRows");
    var ogears = $("#orderGear2").yiiGridView("getSelectedRows");
    var extra = $("#extraItem").yiiGridView("getSelectedRows");
    var modal = $("#packlist_modal");
    var packlist = $("#gearactionform-action").val();

    if (packlist.substr(0,8)=="packlist")
    {
        pack_id = packlist.substr(9, packlist.length);
        $.ajax({
                    url: "'.$modalPacklistUrl.'",
                    type: "post",
                    async: false,
                    data: {gears:gears, ogears:ogears, extra:extra, packlist_id:pack_id},
                    success: function(data) {
                        modal.modal("show").find(".modalContent").empty().append(data);
                    },
                    error: function(data) {
                            
                    }
                }); 
    }
    if (packlist =="delete")
    {
        alert("usuwamy");
        $.ajax({
                    url: "'.$groupDeleteUrl.'",
                    type: "post",
                    async: false,
                    data: {gears:gears, ogears:ogears, extra:extra},
                    success: function(data) {
                        $("#tab-gear").empty();
                        $("#tab-gear").load("'.Url::to(['event/gear-tab', 'id'=>$model->id]).'");
                    },
                    error: function(data) {
                            
                    }
                }); 
    }
    if (packlist =="change_time")
    {
        alert("zmieniamy daty");
    }
    return false;
}).submit(function(e){e.preventDefault();});

$(".category-chackbox").click(function(){
    var c = $(this).prop("checked");
    var category = $(this).data("category");
    $("#allGearTable").find("tr").each(function(){
        cat = $(this).data("main-category");
        cat2 = $(this).data("sub-category");
        if ((category==cat)||(category==cat2))
        {
            $(this).find(":checkbox").prop("checked", c);
        }
    });
});
    ');
