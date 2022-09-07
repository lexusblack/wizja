<?php

use common\models\GearItem;
use common\models\Gear;
use common\models\OutcomesGearOur;
use common\models\RentGearItem;
use common\models\RentOuterGear;
use kartik\editable\Editable;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $model \common\models\Rent; */
$user = Yii::$app->user;

$not_returned_gear = '';
$gear_our_outt = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$model->id])->andWhere(['>', 'quantity', 0])->all();

foreach ($gear_our_outt as $gear)
{
    $gear_model = Gear::findOne($gear->gear_id);
    if ($gear_model->no_items)
    {
            $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name  . "</td><td></td></tr>";

    }else{
            
            $numbers = GearItem::find()->where(['gear_id'=>$gear_model->id, 'rent_id'=>$model->id])->orderBy(['number'=>SORT_ASC])->all();
            $num = "";
            foreach ($numbers as $n)
            {
                if ($num!="")
                    $num.=", ";
                $num .=$n->number;
            }
            $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name  . "</td><td>".$num."</td></tr>";
    }
} ?>
<div class="panel-body">
<h3><?=  Yii::t('app', 'Sprzęt') ?></h3>
<div class="row">
    <div class="col-md-12">
                <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Packlista'), ['packing-list', 'id' => $model->id], ['class' => 'btn btn-success', 'target'=>'_blank']);?>
        <?php
        if ((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))) { 
        if ($user->can('eventRentsEdit')) {
            echo Html::a( Yii::t('app', 'Magazyn'), ['warehouse/assign', 'id'=>$model->id, 'type'=>'rent'], ['class'=>'btn btn-success']);
        }
        if ($user->can('eventRentsEdit')) {
            $offers = $model->getOffersAccepted();
            if (isset($offers['error']) && $offers['error']) {
             echo Html::a( Yii::t('app', 'Dodaj z oferty'), '#', ['class'=>'btn btn-default', 'title'=>Yii::t('app', 'Brak zaakceptowanej oferty'), 'onclick'=>'alert("'.Yii::t('app', 'Brak zaakceptowanej oferty').'"); return false;']); 
             }else{
             echo Html::a( Yii::t('app', 'Dodaj z oferty'), ['warehouse/assign-gear-item-to-offer', 'event_id'=>$model->id, 'type'=>'rent'], ['class'=>'btn btn-success']);               
            }

        }
        }
        if ($user->can('eventRentsMagazin')) {
            echo Html::a( Yii::t('app', 'Wydaj sprzęt'), ['outcomes-warehouse/create', 'rent' => $model->id], ['class'=>'btn btn-primary', 'target' => '_blank']);
            echo Html::a( Yii::t('app', 'Przyjmij sprzęt'), ['incomes-warehouse/create', 'rent' => $model->id, 'onlyEvent'=>1], ['class'=>'btn btn-primary', 'target' => '_blank']);
        }
        if ($not_returned_gear!="") {
            echo Html::a(Yii::t('app', 'Niezwrócony sprzęt'), '#tab-gear', ['class'=>'btn btn-danger', 'id' => 'display-not-returned-gear']);
        }
        if ($user->can('gearWarehouseOutcomesView')) {
            foreach ($model->outcomesForRents as $outcome) {
                echo ' ' . Html::a(Yii::t('app', 'Wydanie') . ' nr: ' . $outcome->outcome_id, ['outcomes-warehouse/view', 'id' => $outcome->outcome_id], ['class' => 'btn btn-warning', 'target' => '_blank']);
            }
        }
        if ($user->can('gearWarehouseIncomesView')) {
            foreach ($model->incomesForRents as $income) {
                echo ' ' . Html::a(Yii::t('app', 'Zwrot') . ' nr: ' . $income->income_id, ['incomes-warehouse/view', 'id' => $income->income_id], ['class' => 'btn btn-primary', 'target' => '_blank']);
            }
        } ?>
    </div>
</div>

<table class="table table-stripped btn-danger" style="width: 50%; margin: auto; display: none;" id="not-returned-table">
    <tr><td><strong><?=  Yii::t('app', 'Niezwrócony sprzęt') ?>:</strong></td><td><strong><?=  Yii::t('app', 'Numery') ?>:</strong></td><td><strong><?=  Yii::t('app', 'Magazyn') ?></strong></td></tr>
    <?= $not_returned_gear ?>
</table>

<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php

            $planned = ['rowOptions'=>[], 'quantity' => [], 'delete' => [], 'gear_numbers' => [], 'case_number' => [], 'after_row' => []];
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedGearModel(),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'rowOptions' => function ($gear) use ($model, &$planned) {
                    // planowany sprzęt
                    return null;
                    if (!in_array($gear->id, $planned['rowOptions'])) {
                        $planned['rowOptions'][] = $gear->id;
                        $planned_event_gears = RentGearItem::find()->where(['planned' => 1])->andWhere(['rent_id' => $model->id])->all();
                        $planned_gears = [];
                        $gear_out_no = 0;
                        $gear_no = 0;

                        foreach ($planned_event_gears as $planned_gear) {
                            $gear_item = GearItem::find()->where(['id' => $planned_gear->gear_item_id])->andWhere(['gear_id' => $gear->id])->one();
                            if ($gear_item) {
                                $planned_gears[] = $gear_item;
                                $gear_no++;
                            }
                        }
                        if ($gear_no == 0) {
                            return ['class' => 'row-all-gear-out-unplanned'];
                        }

                        $outcomes = $model->outcomesForRents;
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
                'afterRow' => function($gear) use ($model, &$planned) {

                    $gearModel = $gear->gear;
                    if (!in_array($gearModel->id, $planned['after_row'])) {
                        $planned['after_row'][] = $gearModel->id;
                        $planned_value = 1;

                        $plann = false;
                        foreach ($model->getGearItems()->where(['gear_id' => $gearModel->id])->all() as $gear) {
                            $event_gear =  RentGearItem::find()->where(['rent_id' => $model->id])->andWhere(['gear_item_id' => $gear->id])->one();
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
                        $gearEvent = RentGearItem::find()->where(['rent_id' => $model->id])->andWhere(['gear_item_id' => $gear_item->id])->andWhere(['planned'=>$planned_value])->one();
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
                                        <td></td>
                                    </tr>';
                    foreach ($case as $case_id => $items) {
                        $tr_class = null;

                        $item_count = 0;
                        foreach ($items as $item) {
                            foreach ($model->outcomesForRents as $outcome) {
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
                        $eventGearItem = RentGearItem::find()->where(['rent_id'=>$model->id])->andWhere(['gear_item_id'=>$items[0]->id])->one();
                        $displayValue =  Yii::t('app', 'Cały event');
                        if ($eventGearItem->start_time != $event_start || $eventGearItem->end_time != $event_end) {
                            $displayValue = $eventGearItem->start_time . " - " . $eventGearItem->end_time;
                        }
                        $widget = null;
                        $editButtons = null;
                            $editButtons = Html::a(Html::icon('pencil'), ['gear-group/update', 'id' => $case_id]);
                            if(Yii::$app->user->can('eventRentsEdit')) {
                                $editButtons .=
                                    Html::a(Html::icon('remove'),
                                        [
                                            '/warehouse/unassign-gear-group',
                                            'event_id' => $model->id,
                                            'case_id' => $case_id,
                                            'type'=>$model->getClassType()
                                        ],
                                        [
                                            'data' => [
                                                'itemsnumber' => $countItems,
                                            ],
                                            'class' => 'remove-assignment-group',
                                        ]

                                    );
                            }
                        $case_table .= "</td><td>" . $editButtons . "</td></tr>";
                    }

                    foreach ($not_case as $item) {
                        $eventGearItem = RentGearItem::find()->where(['rent_id' => $model->id])->andWhere(['gear_item_id' => $item->id])->one();
                        if ($eventGearItem->start_time == $event_start && $eventGearItem->end_time == $event_end) {
                            $display_value =  Yii::t('app', 'Cały event');
                        }
                        else {
                            $display_value = $eventGearItem->start_time . " - " . $eventGearItem->end_time;
                        }

                        $edit_time = null;
                        $remove_link = null;
                        if (Yii::$app->user->can('eventRentsEdit')) {
                                $remove_link = Html::a(Html::icon('remove'), ['/warehouse/assign-gear',
                                    'id' => $model->id,
                                    'type' => $model->getClassType()], ['data' => ['itemId' => $item->id, 'add' => 0,
                                    'name' => $item->name], 'class' => 'remove_gear_item']);
                            }
                        $tr_class = null;
                        foreach ($model->outcomesForRents as $outcome) {
                            if (OutcomesGearOur::find()->where(['outcome_id' => $outcome->id])->andWhere(['gear_id' => $item->id])->count() == 1) {
                                $tr_class = 'row-all-gear-out-planned';
                            }
                        }

                        $case_table .= "<tr class='".$tr_class."'>";
                        $case_table .= "<td></td>";
                        $case_table .= "<td>".$item->getBarCodeValue()."</td>";
                        $case_table .= "<td>".$item->number."</td>";
                        $case_table .= "<td>" . $remove_link . "</td>";

                        $case_table .= "</tr>";
                    }

                    $case_table .= '</table>';

                    return "<tr class='sub_models'><td colspan='7'  style='padding:0; border:0;'>".$case_table."</td></tr>";
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
                        'label' => Yii::t('app','Zdjęcie'),
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $column)
                        {
                            return Html::a(Html::img($model->gear->getPhotoUrl(), ['width'=>50]), ['gear/view', 'id'=>$model->gear->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label'=> Yii::t('app', 'Model'),
                        'value'=>function ($model, $key, $column)
                        {
                            return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label' =>  Yii::t('app', 'Gear'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_name = $categories[1]->name;
                            }else{
                                $category_name = $category->name;
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
                        'format'=>'raw',
                        'attribute'=>'quantity',
                        'value' => function($model) use ($user)
                        {
                            if (($user->can('eventRentsMagazin'))&&((!$model->rent->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent')))) {
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
                                            'id'=>'gear-assignment-form'.$model->gear_id
                                        ],
                                        'action' =>['assign-gear', 'id'=>$model->rent->id, 'type'=>'rent'],
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
                                        'data-quantity'=>$model->quantity
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
                        'format' =>'raw',
                        'value' => function ($gear) use ($model){
                            $remove_link = "";
                            if ((Yii::$app->user->can('eventRentsMagazin'))&&((!$model->getBlocks('event'))||(Yii::$app->user->can('eventEventBlockEvent')))) {
                                $remove_link = Html::a(Html::icon('remove'), ['/warehouse/remove-gear',
                                    'id' => $model->id,
                                    'type' => $model->getClassType(),
                                    'noItem'=>1],
                                     ['data' => ['itemId' => $gear->gear_id,
                                    'name' => $gear->gear->name], 'class' => 'remove_gear_item']);
                            }
                            return $remove_link;
                        }
                    ],
                ],
            ])
        ?>

<h5><?php echo Yii::t('app', 'Sprzęt zarezerwowany u wypożyczającego'); ?></h5>
        <?php
        //echo var_dump($model->getAssignedOuterGears());
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedOuterGears(),
                'id'=>'orderGear',
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
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            return $gear_no->quantity;
                        }
                    ],
/*
                    [
                        'label' => Yii::t('app', 'Zamówienie'),
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->order_id)
                                return Html::a(Yii::t('app', 'Zamówienie nr').' '.$gear_no->order_id, ['/order/view', 'id' => $gear_no->order_id]);
                            else 
                                return "-";
                        }
                    ],
                    */
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'value' => function ($gear) use ($model) {
                            $EventOuterGear = RentOuterGear::find()->where(['rent_id' => $model->id])->andWhere(['outer_gear_id' => $gear->id])->one();
                            return $EventOuterGear->start_time . " - " . $EventOuterGear->end_time;
                        }
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
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'rent_id'=>$model->id],
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
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
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
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'rent_id'=>$model->id],
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
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
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
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'rent_id'=>$model->id],
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
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
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
                                    'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->id, 'rent_id'=>$model->id],
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
                            $gear_no = $model->getRentOuterGears()->where(['outer_gear_id'=>$gear->id])->one();
                            if ($gear_no->user_id)
                                return $gear_no->user->displayLabel;
                            else 
                                return "-";
                        }
                    ],
                ],
            ])
            ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php

$this->registerJs('

$(".remove_gear_item").click(function(e){
    e.preventDefault();
    var quantityElement = $(this).parent().parent().parent().parent().parent().parent().prev().find(":nth-child(4)");
    var quantity = parseInt(quantityElement.html());
    quantityElement.html(quantity-1); 
    
    $(this).parent().parent().remove();
    if (quantity == 1 || $(this).data("name") == "_ILOSC_SZTUK_") {
        if (quantityElement.parent().next().hasClass("sub_models")) {
            quantityElement.parent().next().remove();
        }
        quantityElement.parent().remove();
    }

    var data = {
        itemid: $(this).data("itemid"),
        add: 0
    };
    $.post($(this).attr("href"), data);
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
            $(this).parent().parent().remove();
        }
    });
});

$("#display-not-returned-gear").click(function(e){
    e.preventDefault();
    $("#not-returned-table").toggle();
});

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
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

');

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

$event = $model;
$type = 'rent';
$eventGearConnectedUrl = Url::to(['warehouse/assign-gear-connected', 'id'=>$event->id, 'type'=>$type]);
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
$this->registerJs('
$(".gear-quantity").on("change", function(e){
    e.preventDefault();
    var form = $(this).closest("form");
    var data = form.serialize();
    var value = $(this).val();
    var input = $(this);
    $.post("'.$eventGearQuantityUrl.'", data, function(response){
         
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
             input.val(input.data("quantity"));
                /*if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }*/
            //brak wolnych egzemplarzy, wyswietlamy okienko z podobnymi
        }
        else
        {
            input.data("quantity", value);
            if (value>0)
            {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
            }
            else{
                toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
            }
        }
        $(".gear-assignment-form").yiiActiveForm("updateAttribute", "gearassignment-quantity", error);
        
    });
   
    
   return false;
});

');

?>