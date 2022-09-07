<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\export\ExportMenu;
use common\components\grid\GridView;
use kartik\helpers\Enum;
use yii\bootstrap\Html;
use kartik\dynagrid\DynaGrid;
$this->title = Yii::t('app', 'Wydarzenia - raport');
$this->params['breadcrumbs'][] = $this->title;
$users = \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]);
$customers = \common\models\Customer::getList();
?>
<div class="event-report-index">

    <h1><?= Html::encode($this->title) ?></h1>
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
                <p><?=Yii::t('app', 'Raport generuje się codziennie w nocy, możesz go odświeżyć szybciej klikając ')?><?=Html::a(Yii::t('app', 'Odśwież raport'), ['calculate'], ['class'=>'btn btn-s btn-primary refresh-report'])?></p>
                
        </div>
        </div>
    <?php 
    $columns = [
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute' => 'name',
                'label' => Yii::t('app', 'Nazwa'),
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->name, ['/event/view', 'id'=>$model->event_id]);

                },
            ],
        [
                'attribute' => 'manager_id',
                'label' => Yii::t('app', 'PM'),
                'value' => function($model) use ($users){
                    if (isset($model->manager_id))
                        return $users[$model->manager_id];
                    else
                        return "-";
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'User', 'id' => 'grid-event-report-search-manager_id']
            ],
        'code',
        [
                'format'=>'html',
                'value'=>function($model) use ($customers)
                {
                    if ($model->customer_id)
                    {
                        $content = Html::a($customers[$model->customer_id], ['/customer/view', 'id' => $model->customer_id]);
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
            ],
        'event_start',
        'event_end',
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
                        return $model->event->getStatusButton();
                    },
                ],
        'location',
        [
                'attribute'=>'paying_date',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Event::getPayingDateList(),
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
                        return \common\models\Event::getPayingDateList()[$model->paying_date];
                    else
                        return "-";
                }
            ],
        ['attribute'=> 'total_value', 'pageSummary'=>true,'format' => 'decimal',],
        ['label'=>Yii::t('app', 'Wartość brutto'), 'value'=>function($model){return $model->total_value*1.23;}, 'pageSummary'=>true,'format' => 'decimal',],
        ['attribute'=> 'total_cost', 'pageSummary'=>true,'format' => 'decimal']];
            foreach (\common\models\ProvisionGroup::find()->all() as $gp)
            {
            $columns[] = 
            [
                'label'=>$gp->name,
                'format'=>'raw',
                'value'=>function($model) use ($gp)
                {
                    $value = \common\models\EventReportProvisions::find()->where(['event_id'=>$model->event_id, 'provision_group_id'=>$gp->id])->asArray()->one();
                    if ($value)
                        return $value['value'];
                    else
                        return 0;
                },
                'pageSummary'=>true,
                'format' => 'decimal',
                'contentOptions'=>[
                    'class'=>'sum-cell provision-cell prov'.$gp->id,
                    'data-provision-name'=>$gp->name,
                    'data-provision-id'=>$gp->id
                ],
            ];
            }

        $columns[] = ['attribute'=> 'total_provision', 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = [
            'label'=>Yii::t('app', 'Koszty+prowizje'),
            'value'=>function($model){
                return $model->total_provision+$model->total_cost;
            },
            'pageSummary'=>true,
            'format' => 'decimal'
        ];
        $columns[] = [
            'label'=>Yii::t('app', 'Zysk'),
            'value'=>function($model){
                return $model->total_value-($model->total_provision+$model->total_cost);
            },
            'pageSummary'=>true,
            'format' => 'decimal'
        ];        
        $columns[] = ['attribute'=> 'total_predicted_cost', 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = ['attribute'=> 'total_predicted_provision', 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = [
            'label'=>Yii::t('app', 'Przew. koszty+prowizje'),
            'value'=>function($model){
                return $model->total_predicted_cost+$model->total_predicted_provision;
            },
            'pageSummary'=>true,
            'format' => 'decimal'
        ];
        $columns[] = [
            'label'=>Yii::t('app', 'Przew. zysk'),
            'value'=>function($model){
                return $model->total_value-($model->total_predicted_cost+$model->total_predicted_provision);
            },
            'pageSummary'=>true,
            'format' => 'decimal'

        ]; 
        $columns[] = [
            'label'=>Yii::t('app', 'Różnica'),
            'value'=>function($model){
                return ($model->total_predicted_cost+$model->total_predicted_provision)-($model->total_provision+$model->total_cost);
            },
            'pageSummary'=>true,
            'format' => 'decimal'
        ]; 
        $columns[] = ['attribute'=> 'paid', 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = ['attribute'=> 'prepaid', 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = ['attribute'=> 'fv_total', 'label'=>"FV brutto", 'pageSummary'=>true,'format' => 'decimal'];
        $columns[] = [
                'attribute' => 'event_type_id',
                'value' => function($model){
                    return \common\models\Event::getTypeList()[$model->event_type_id];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\Event::getTypeList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true, 'multiple'=>true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-event-report-search-event_model_id']
            ];
        $columns[] = [
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->event_model_id)
                        return \common\models\Event::getEventTypeList()[$model->event_model_id];
                    else
                        return "-";

                },
                'attribute'=>'event_model_id',
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
                                return $model->event->getAdditionalStatut($s->id, 1);
                            },
                        ];
                }

            }
                    $columns[] =
                        [
                            'label'=>Yii::t('app', 'Aktualizacja'),
                            'format'=>'raw',
                            'value' => function($model, $key, $index, $column)
                            {
                                return $model->create_time." ".Html::a(Yii::t('app', 'Odśwież raport'), ['calculate', 'event_id'=>$model->event_id], ['class'=>'btn btn-xs btn-primary refresh-report']);
                            },
                        ];
    ?>
        <?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => true,
            //'pageSummaryPosition'=>'top',
            'floatHeader'=>true,
            'floatHeaderOptions' => [
            'position' => 'absolute'
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap',
            ],
            'id'=>'events-report-grid',

        
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
        'options'=>['id'=>'dynagrid-events-report'],

        'columns' => $columns,
    ]); ?>


</div>
<?php
$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}
');

$this->registerJs('
    var offset = 0;

    $(".refresh-report").click(function(e){
        e.preventDefault();
        
        $(this).prop( "disabled", true );
        //$(this).hide();
        var $b = $(this).parent();
        $(this).parent().empty();

        $b.append("Trwa aktualizowanie raportów...");
        calculateReports(offset, $(this).attr("href"));
    })

    function calculateReports(offset, url)
    {
        $.ajax({
                  url: url+"?offset="+offset,
                  success: function(response){
                    if (response.success==1)
                    {
                        offset +=50;
                        calculateReports(offset, url);
                    }else{
                        location.reload();
                    }
                    
                  }
                });
    }
    ');