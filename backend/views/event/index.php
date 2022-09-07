<?php

use common\models\Event;
use common\models\GearItem;
use common\models\OuterGear;
use common\components\grid\GridView;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
use common\components\grid\LabelColumn;
use kartik\editable\Editable;
use yii\bootstrap\Modal;
//use kartik\grid\GridView;
use kartik\helpers\Enum;
use yii\bootstrap\Html;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
Modal::begin([
    'id' => 'offer-notes',
    'header' => Yii::t('app', 'Notatki'),
    'class' => 'modal',
        'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'change-additional-statut',
    'header' => Yii::t('app', 'Zmień status'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
$types = \common\models\Event::getTypeList();
$event_types = \common\models\Event::getEventTypeList();

$this->title = Yii::t('app', 'Wydarzenia');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="event-index">
    <p>
        <?php
        if ($user->can('eventsEventAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) . " ";
            echo Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Raport .xls - finanse'), ['excel', 'type'=>1], ['class' => 'btn btn-success']) . " ";
            echo Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Raport .xls - pracownicy'), ['excel', 'type'=>2], ['class' => 'btn btn-success']) . " ";
        }
        if (($user->can('menuInvoices'))&&(Yii::$app->params['companyID']=="wizja"))
        {
            echo Html::a('<i class="fa fa-star"></i> ' . Yii::t('app', 'Raport finansowy'), ['/event-report'], ['class' => 'btn btn-success']) . " ";
        }
        if (Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_OFF) {
            echo Html::a(Yii::t('app', 'Wyślij wszystkie powiadomienia'), ['send-all-events-notifications'], ['class' => 'btn btn-success send-noti']);
        }

        ?>
        <?php 
        $sectionList = [Yii::t('app', 'Suma')=>Yii::t('app', 'Suma'), Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa')];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        }
        ?>
    </p>
        <div class="row">
        <div class="col-md-4">
        <?php echo Html::activeHiddenInput($searchModel, 'useRange', ['class'=>'grid-filters', 'id'=>'date-use-range']); ?>
        <?php echo \kartik\daterange\DateRangePicker::widget([
                    'options' => ['class'=>' form-control'],
                    'model' => $searchModel,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'startAttribute' => 'dateStart',
                    'endAttribute' => 'dateEnd',
                    'startInputOptions' => [
                        'class'=>'grid-filters',
                    ],
                    'endInputOptions' => [
                        'class'=>'grid-filters',
                    ],
                    'pluginOptions' => [
                    'linkedCalendars'=>false,
                        'locale'=>[
                            'format'=>'Y-m-d'
                        ]
                    ],
                    'pluginEvents' => [
                        'apply.daterangepicker'=>'function(ev,picker){
                            $("#date-use-range").val(1).trigger("change");
                        }',
                    ]
                ]);
                ?>
                </div>
                <div class="col-md-8">
        <?= Html::dropDownList(null, Yii::t('app', 'Suma'), $sectionList, ['class' => 'changeSection form-control pull-right', 'style'=>' width:200px']) ?>
        </div>
        </div>
        
    <div class="panel_mid_blocks">
        <div class="panel_block">

        <?php 
         
        $columns = [['class' => CheckboxColumn::className()],
            ['class' =>SerialColumn::className()]];
        if ($user->can('eventsEventEditStatus'))
        $columns[] = [
                    'attribute'=>'status',
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Event::getStatusList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'multiple'=>true
                        ],
                    ],
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/event/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\Event::getStatusList2($model->status),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                    'value' => function($model, $key, $index, $column)
                    {
                        return $model->getStatusButton();
                    },
                ];
        else
        $columns[] =
                [
                    'attribute'=>'status',
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Event::getStatusList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'multiple'=>true
                        ],
                    ],
                    'value' => function($model, $key, $index, $column)
                    {
                        return $model->getStatusButton();
                    },
                ];


        $columns[]= 
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Wydarzenie'),
                'format'=>'html',
                'value'=>function($model) use ($user)
                {
                    $content = Html::a($model->name.' ['.$model->code.']', ['view', 'id' => $model->id]);
                    if (Yii::$app->params['companyID']!="redbull") {
                    if ($model->customerNotes)
                    {
                        $content .= Html::a(' <span class="label label-info"><i class="fa fa-comments"></i>'.count($model->customerNotes).'</span>', ['show-notes', 'id'=>$model->id], ['class'=>'show-notes']);
                    }
                    $content .= Html::a(' <span class="label"><i class="fa fa-plus"></i> '.Yii::t('app', 'Notatka').'</span>', ['add-note', 'id'=>$model->id], ['class'=>'show-notes']);
                    if ($user->can('eventsEventAdd')) {
                        $content .= " ".Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Duplikuj'), ['create', 'event_id'=>$model->id], ['class' => 'btn btn-success btn-xs']) . " ";
                    }
                    }
                    return $content;
                }
            ];
            if ($user->can('eventsEventEditStatus')){
            $i=0;
            foreach (\common\models\EventAdditionalStatut::find()->where(['active'=>1])->all() as $s)
            {
                $i++;
                if ($s->showToUser())
                {
                        $columns[] =
                        [
                            'label'=>$s->name,
                            
                            'attribute'=>'statut'.$i,
                            'format' => 'raw',
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter'=>$s->getStatusList(1),
                            'filterWidgetOptions' => [
                                //                    'data'=>\common\models\Event::getList(),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                    'multiple'=>true
                                ],
                            ],
                            'value' => function($model, $key, $index, $column) use ($s)
                            {
                                return Html::a($model->getAdditionalStatut($s->id, 1), ['change-additional-status2', 'id'=>$model->id, 'status'=>$s->id], ['class'=>'change-additional-statut', 'id'=>'statut-'.$model->id.'-'.$s->id]);
                            },
                        ];
                }

            }
        }

        $columns[]= 
            [
                'label'=>Yii::t('app', 'Status'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $status = $model->getTaskStatus();
                    if ($status['task']==0)
                        return '<small>'.Yii::t('app', 'Brak zadań').'</small>';
                    $content = '<small>'.Yii::t('app', 'Ukończono').': '.$status['status'].'%</small>
                    <div class="progress progress-mini">
                    <div style="width: '.$status['status'].'%;" class="progress-bar"></div>
                    </div>';
                    return $content;
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ];


        $columns[]= 
            [
                'format'=>'html',
                'value'=>function($model) use ($types)
                {
                    return $types[$model->type];

                },
                'attribute'=>'type',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Event::getTypeList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ]
            ];
            $columns[]= 
            [
                'format'=>'html',
                'value'=>function($model) use ($event_types)
                {
                    if ($model->event_type)
                        return $event_types[$model->event_type];
                    else
                        return "-";

                },
                'attribute'=>'event_type',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Event::getEventTypeList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ]
            ];
            if (Yii::$app->user->can('eventsEventEditEyeClientDetails'))
            {
            $columns[] =
            [
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->location)
                    {
                        $content = Html::a($model->location->name, ['/location/view', 'id' => $model->location->id]);
                        return $content;
                    }else{
                        return $model->address;
                    }

                },
                'attribute'=>'location_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Location::getModelList(false, 'displayLabel'),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ];
            $columns[] =
            [
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->customer)
                    {
                        $content = Html::a($model->customer->displayLabel, ['/customer/view', 'id' => $model->customer->id]);
                        return $content;
                    }else{
                        return "-";
                    }

                },
                'filter' => \common\models\Customer::getList(),
                'attribute' => 'customer_id',
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ];
            }
            $columns[] =
            [
                'value'=>'manager.displayLabel',
                'filter' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]),
                'attribute' => 'manager_id',
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ];

            $columns[] =
            [
                'label'=>Yii::t('app', 'Od - do'),
                'attribute'=>'event_start',
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->event_start,'short');
                    $end = Yii::$app->formatter->asDateTime($model->event_end, 'short');
                    return $start.' <br /> '.$end;
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ];
            if (Yii::$app->user->can('eventsEventEditEyeFinance'))
            {
            $columns[] =
            [
                'label'=>Yii::t('app', 'Wartość'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventValueAll();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div' style='float:left'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    $content.=' <span class="label label-success" title="'.Yii::t('app', 'Liczba zaakceptowanych ofert').'">'.$model->getFinancesOffersCount().'</span>';
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell value-cell',
                ],
            ];

            $columns[] =
            [
                'label'=>Yii::t('app', 'Koszt'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventCosts();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell cost-cell',
                ],
            ];
            if (Yii::$app->params['companyID']!="wizja")
        {
            $columns[] =
            [
                'label'=>Yii::t('app', 'Zysk'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventProfits();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell profit-cell',
                ],
            ];
            $columns[] =
            [
                'label'=>Yii::t('app', 'Zaliczka'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $v = $model->getEventPMcost();
                    return "<div class='value-div Suma-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell zaliczka-cell',
                ],
            ];
            foreach (\common\models\ProvisionGroup::find()->all() as $gp)
            {
            $columns[] = 
            [
                'label'=>$gp->name,
                'format'=>'raw',
                'value'=>function($model) use ($gp)
                {
                    $values = \common\models\EventProvisionValue::find()->where(['event_id'=>$model->id, 'provision_group_id'=>$gp->id])->asArray()->all();
                        $content = "";
                            foreach ($values as $v)
                            {
                                $content .= "<div class='value-div ".$v['section']."-div'>".Yii::$app->formatter->asCurrency($v['value'])."</div>";
                            }
                    if ($content=="")
                            $content = "<div class='value-div ".Yii::t('app', 'Suma')."-div'>".Yii::$app->formatter->asCurrency(0)."</div>";

                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell provision-cell prov'.$gp->id,
                    'data-provision-name'=>$gp->name,
                    'data-provision-id'=>$gp->id
                ],
            ];
            }
            
            $columns[] =
            [
                'label'=>Yii::t('app', 'Suma kosztów'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $profit = $model->getEventProfits();
                    $values = $model->getEventValueAll();
                    foreach ($model->getGProvisions()as $prov){ 
                        foreach ($profit as $k => $v): $profit[$k]-=$prov['sections'][$k]; endforeach;
                        }
                    $content = "";
                    foreach ($profit as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($values[$k]-$v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell cost-total-cell',
                ],
            ];            
            $columns[] =
            [
                'label'=>Yii::t('app', 'Zysk końcowy'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $profit = $model->getEventProfits();
                    foreach ($model->getGProvisions()as $prov){ 
                        foreach ($profit as $k => $v): $profit[$k]-=$prov['sections'][$k]; endforeach;
                        }
                    $content = "";
                    foreach ($profit as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell profit2-cell',
                ],
            ];

            $columns[] =
            [
                'label'=>Yii::t('app', 'Szacowane koszty'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventPredictedCost();
                    foreach ($model->getEventPredictedProvisions()as $k=>$v){ 
                        $value[$k]+=$v;
                        }
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell szacowane-cell',
                ],
            ];
        }
            $columns[] =
            [
                'attribute'=>'paying_date',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>Event::getPayingDateList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ],
                'value'=>function($model){
                    if ($model->paying_date)
                        return Event::getPayingDateList()[$model->paying_date];
                    else
                        return "-";
                }
            ];
            $columns[] =
            [
                'attribute'=>'create_time'
            ];
            $columns[] =
            [
                'label'=>Yii::t('app', 'Zapłacono'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    return "<div class='value-div ".Yii::t('app', 'Suma')."-div'>".Yii::$app->formatter->asCurrency($model->getEventPaid())."</div>";
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell paid-cell',
                ],
            ];
            $columns[] =
            [
                'label'=>Yii::t('app', 'FV'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $value= "";
                    foreach ($model->invoices as $invoice)
                    $value .= "<a href='/admin/finances/invoice/view?id=".$invoice->id."'>".$invoice->fullnumber."</a> ";
                    return $value;
                },
            ];
        }
        foreach(\common\models\EventFieldSetting::find()->where(['active'=>1])->andWhere(['column_in_list'=>1])->all() as $field)
        {
            $columns[] =
            [
                'label'=>$field->name,
                'value'=>function($model) use ($field)
                {
                    return $model->getFieldValue($field->id);
                },
            ];
        }
$columns[] = 
            [
                'label' =>  Yii::t('app', 'Niezwrócony sprzęt'),
                'format'=>'html',
                'value' => function ($model) {
                    $result = '';
                    $notReturnedGear = $model->getWarehouseGearDifference()[0];
                    $gear_our = [];
                    $gear_group = [];
                    foreach ($notReturnedGear as $gear_id => $quantity) {
                        $item = GearItem::find()->where(['id' => $gear_id])->one();
                            if ($item->gear->no_items) {
                                if ($item->outcomed>0){
                                    if ($quantity>$item->outcomed)
                                        $quantity = $item->outcomed;
                                    $result .= "<div style='white-space: nowrap;'>" . $quantity . "x " . $item->gear->name ."</div>";
                                }
                                
                            }
                            else {
                                $gear_our[$item->gear_id][] = $item;
                            }
                    }
                    foreach ($gear_our as $gear_id => $items) {
                        $numbers = "";
                        $count = 0;
                        foreach ($items as $item) {
                            if ($item->outcomed){
                                $numbers .= $item->number . ", ";
                                $count++;
                            }
                        }
                        if ($numbers!="")
                            $result .= "<div style='white-space: nowrap;'>" . $count . "x " . $items[0]->name . ", ". Yii::t('app', 'numer').": " . $numbers ."</div>";

                    }

                    if ($result != '') {
                        $result =  "<div class='display_none'>".$result."</div><span>". Yii::t('app', 'Niezwrócony sprzęt')."</span>";
                    }
                    return $result;
                },
                'contentOptions' => function ($model) {
                    if ($model->countNotReturnedGears() > 0) {
                        return ['style'=>'background-color:red; color:white;', 'class' => 'outer_gear'];
                    }
                    return [];
                }
            ];
            $columns[] =

            [
                'class'=>\common\components\ActionColumn::className(),
                'buttons' => [

                            'delete' => function ($url, $model) {

                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [

                                            'title' => Yii::t('app', 'Usuń'),

                                            'data-confirm' => Yii::t('yii', 'Na pewno chcesz usunąć? Kasujesz wszystkie rezerwacje, a także wydania i przyjęcia w tym wydarzeniu.'),
                                                'data-method' => 'post',

                                    ]);

                            },
                ],
                'visibleButtons' => [
                        'update' => $user->can('eventEventEditPencil'),
                        'delete' => $user->can('eventEventDelete'),
                        'view' => $user->can('eventEventEditEye'),
                ]
            ]; 

        ?>
    <?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => true,
                 
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'id'=>'events-grid',

        
            'toolbar' => [
                [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')+5), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-events'],

        'columns' => $columns,
    ]); ?>
</div>
    </div>
</div>

<?php


$this->registerJs('

$(".change-additional-statut").click(function(e){
        e.preventDefault();
        $("#change-additional-statut").find(".modalContent").empty();
        $("#change-additional-statut").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".change-additional-statut").on("contextmenu",function(){
       return false;
    });

$(".value-div").hide();
$("."+$(".changeSection").val()+"-div").show();
$(".changeSection").change(function(){
    $(".value-div").hide();
    $("."+$(this).val()+"-div").show();
    sumTable();
});

function sumTable(){
    
    var keys = $("#events-grid").yiiGridView("getSelectedRows");
    var totals = [0,0,0,0,0,0,0,0,0, 0];
    var positions = [0,0,0,0,0,0,0,0,0, 0];
    var provisions = [];
    var provisionsIndex = [];
    var provisionsNames = [];
    var $dataRows = $("tbody tr");
    $dataRows.each(function(){
    
        $(this).find(".sum-cell").each(function(i){
            var currentKey = $(this).closest("tr").data("key");
            var sumRow = false;
            
            // for all rows or selected 
            if (keys.length<1 || $.inArray(currentKey, keys)!=-1) {
                sumRow = true;
            }
            
            if (sumRow==true) {
                var val = $(this).html();
                var el2 = $(this).find("."+$(".changeSection").val()+"-div");
               
                if (el2.length) {
                    val = el2.html();
                }else{
                    val = "0";
                }
                
                if ("'.Yii::$app->formatter->decimalSeparator.'".length > 0) {
                    val = val.replace("'.Yii::$app->formatter->decimalSeparator.'", ".");
                }
                val = val.replace("'.Yii::$app->formatter->thousandSeparator.'", "");
                val = val.replace(/[^0-9.,-]+/ig, "");
                val = val.replace(",", ".");
                if ($(this).hasClass("value-cell"))
                {
                    totals[0] += parseFloat(val);
                    positions[0] = $(this).index();
                }
                if ($(this).hasClass("cost-cell"))
                {
                    totals[1] += parseFloat(val);
                    positions[1] = $(this).index();
                }
                if ($(this).hasClass("zaliczka-cell"))
                {
                    totals[2] += parseFloat(val);
                    positions[2] = $(this).index();
                }
                if ($(this).hasClass("szacowane-cell"))
                {
                    totals[3] += parseFloat(val);
                    positions[3] = $(this).index();
                }
                if ($(this).hasClass("provision-cell"))
                {
                    if (typeof provisions[$(this).data("provision-id")] !== "undefined") 
                    {
                        
                    }else{
                        provisions[$(this).data("provision-id")] = 0;
                    }
                    provisions[$(this).data("provision-id")] +=  parseFloat(val);
                    provisionsIndex[$(this).data("provision-id")] = $(this).index();
                    provisionsNames[$(this).data("provision-id")] = $(this).data("provision-name");
                }
                if ($(this).hasClass("profit-cell"))
                {
                    totals[5] += parseFloat(val);
                    positions[5] = $(this).index();
                }
                if ($(this).hasClass("profit2-cell"))
                {
                    totals[4] += parseFloat(val);
                    positions[4] = $(this).index();
                }
                if ($(this).hasClass("paid-cell"))
                {
                    totals[6] += parseFloat(val);
                    positions[6] = $(this).index();
                }
                if ($(this).hasClass("szacowane-profit-cell"))
                {
                    totals[7] += parseFloat(val);
                    positions[7] = $(this).index();
                }
                if ($(this).hasClass("different-cell"))
                {
                    totals[8] += parseFloat(val);
                    positions[8] = $(this).index();
                }  
                if ($(this).hasClass("cost-total-cell"))
                {
                    totals[9] += parseFloat(val);
                    positions[9] = $(this).index();
                }               
            }
            
        });
    });

    totals[5] = totals[0]-totals[1];
    labels = [];
    labels[0] = "Wartość";
    labels[1] = "Koszt";
    labels[5] = "Zysk";
    labels[2] = "Zaliczka";
    labels[4] = "Zysk k.";
    labels[3] = "Szacowane";
    labels[6] = "Zapłacono";
    labels[7] = "Szac. zysk";
    labels[8] = "Różnica";
    labels[9] = "Koszty+prow";
    for(var j=0;j<10; j++) {
        if (positions[j]!=0)
        {
            $(".kv-page-summary td").eq(positions[j]).html(labels[j]+": "+numberWithCommas(totals[j].toFixed(2)));
            $(".kv-page-summary td").eq(positions[j]).css("white-space", "nowrap");
        }
    }

    for (var j=0; j<provisions.length; j++)
    {
        if (provisions[j])
        {
            $(".kv-page-summary td").eq(provisionsIndex[j]).html(provisionsNames[j]+": "+numberWithCommas(provisions[j].toFixed(2)));
            $(".kv-page-summary td").eq(provisionsIndex[j]).css("white-space", "nowrap");
        }
    }
    
}

sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});



$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


$(".outer_gear").click(function(){
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

$(".send-noti").click(function(e){
    e.preventDefault();
    var el = $(this);
    $.post($(this).prop("href"), null, function(){
        alert("Powiadomienia zostały wysłane");
        el.hide("slow");
    });
}); 

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}
');

$this->registerJs('
    $(".show-notes").click(function(e){
        $("#offer-notes").find(".modalContent").empty();
        e.preventDefault();
        $("#offer-notes").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".show-notes").on("contextmenu",function(){
       return false;
    });
'); 