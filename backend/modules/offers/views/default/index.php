<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
use common\components\grid\LabelColumn;
use kartik\helpers\Enum;
use kartik\dynagrid\DynaGrid;
use yii\bootstrap\Modal;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Oferty');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
$formatter = Yii::$app->formatter;
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
?>
<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($user->can('menuOffersAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success'])." ";
            echo Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Pobierz zaznaczone'), ['#'], ['class' => 'btn btn-primary download-all']);
        }

        ?>
    </p>
        <div class="title_box row">
            <div class="col-lg-6">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $months = array_merge([Yii::t('app', 'Wszystkie')], $months); ?>
                <?php echo Html::a(Html::icon('arrow-left'), ['index', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, 2025, true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::dropDownList('searchtype',$st, [1=>'Data eventu', 2=>'Data oferty'], ['class'=>'form-control date-drop', 'id'=>'searchtype']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['index', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                </form>
                <?php echo Html::activeHiddenInput($searchModel, 'useRange', ['class'=>'grid-filter', 'id'=>'date-use-range']); ?>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/offer/default/index?m="+$("#month").val()+"&y="+$("#year").val()+"&st="+$("#searchtype").val();
                        });
                    ');
                ?>
            </div>
            <div class="col-lg-6">
        <?php 
        $sectionList = [Yii::t('app', 'Suma')=>Yii::t('app', 'Suma'), Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa')];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        } ?>
            <?= Html::dropDownList(null, Yii::t('app', 'Suma'), $sectionList, ['class' => 'changeSection form-control pull-right', 'style'=>' width:200px']) ?>
            </div>
</div>
    <div class="panel_mid_blocks">
        <div class="panel_block">

        <?php

        $columns = [['class' => CheckboxColumn::className()],
            [
                'attribute'=>'id',
                'label'=>Yii::t('app', 'Nr'),   
                'format'=>'raw',
                'value'=>function($model) use ($user)
                {
                        $content = "";
                    if ($user->can('menuOffersEdit'))
                    if (($model->blocked)||($model->offerStatut->blocked))
                        {
                            if ($model->blocked)
                            {
                                $content = Html::a(' <span class="label label-danger"><i class="fa fa-lock"></i> '.Yii::t('app', 'Odblokuj').'</span>', ['unblock', 'id'=>$model->id], ['title'=>'Kliknij, aby odblokować']);
                            }else{
                                $content = ' <span class="label label-danger" title="Oferta zablokowana, zmień status, żeby odblokować"><i class="fa fa-lock"></i></span>';
                            }
                            
                        } 
                    return $model->id." ".$content;
                }
                     
         ]];
         if ($user->can('menuOffersAdd'))
         {
        $columns[] = [
                    'attribute'=>'status',
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Offer::getStatusList(),
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
                                    'action'=>['/offer/default/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\Offer::getStatusList($model->status),
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
         }else{
            $columns[] = [
                    'attribute'=>'status',
                    'format' => 'html', 
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Offer::getStatusList(),
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
         }
         $columns[] = [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model) use ($user)
                {
                    if (!$user->can('menuOffersEdit'))
                    {
                        $content = Html::a($model->name, ['pdf2', 'id' => $model->id]);
                    }else{
                        
                        $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    }
                    
                    if ($model->customerNotes)
                    {
                        $content .= Html::a(' <span class="label label-info"><i class="fa fa-comments"></i>'.count($model->customerNotes).'</span>', ['show-notes', 'id'=>$model->id], ['class'=>'show-notes']);
                    }
                    $content .= Html::a(' <span class="label"><i class="fa fa-plus"></i> '.Yii::t('app', 'Notatka').'</span>', ['add-note', 'id'=>$model->id], ['class'=>'show-notes']);
                    return $content;
                }
                
            ];

        $columns[] = [
                'attribute'=>'customer_id',
                'format' => 'html',
                'filter'=> \common\models\Customer::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    $content = Html::a($model->customer->name." [".$model->customer->nip."]", ['/customer/view', 'id' => $model->customer_id]);
                    return $content;
                },
            ];

            if ($user->can('eventsEventEditEyeFinance'))
            {
                $columns[] = [
                'label'=>Yii::t('app', 'Wartość'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getOfferValues();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        if ($model->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                            $v = $v*$model->exchange_rate;
                        }
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'visible'=>$user->can('eventsEventEditEyeFinance'),
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
                ];

                $columns[] = [
                'label'=>Yii::t('app', 'Koszt'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getOfferCosts();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'visible'=>$user->can('eventsEventEditEyeFinance'),
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
                ];

                $columns[] = [
                'label'=>Yii::t('app', 'Zysk'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getOfferProfits();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'visible'=>$user->can('eventsEventEditEyeFinance'),
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
                ];


            }


                $columns[] = [
                'attribute'=>'location_id',
                'filter'=> \common\models\Location::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    if (isset($model->location))
                        return $model->getLocationLabel();
                    else
                        return $model->address;
                },
                ];

                $columns[] = [
                'attribute'=>'manager_id',
                'filter'=> \common\models\User::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->manager_id)
                        return $model->manager->displayLabel;
                    else
                        return "-";
                },
                ];

                $columns[] = [
                'label'=>Yii::t('app', 'Wysłano'),
                'format'=>'html',
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->offerSends)
                    {
                        $content = "";
                        foreach ($model->offerSends as $os)
                        {
                            $content .= $os->recipient."<br/>".substr($os->datetime, 0, 16)."<br/>";
                        }
                        return $content;
                    }else{
                        return "-";
                    }

                },
                ];

                $columns[] = [
                'label'=>Yii::t('app', 'Wydarzenie'),
                'format'=>'html',
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->event_id)
                    {
                        $list = \common\models\Event::getList();
                        return Html::a($list[$model->event_id], ['/event/view', 'id'=>$model->event_id]);
                    }else{
                        if ($model->rent_id)
                        {
                            $list = \common\models\Rent::getList();
                            return Html::a($list[$model->rent_id], ['/rent/view', 'id'=>$model->rent_id]);
                        }else{
                            return "-";
                        }
                    }

                },
            ];

            $columns[] = 'offer_date';

            if ($user->can('menuOffersViewDuplicate'))
            {
                $columns[] = [
                'label' => Yii::t('app', 'Duplikuj'),
                'format' => 'html',
                'visible' => $user->can('menuOffersViewDuplicate'),
                'value' => function ($model) {
                    return Html::a('<i class="fa fa-copy"></i>', ['/offer/default/duplicate', 'id' => $model['id']], ['class'=>'btn-sm btn btn-warning btn-circle']);
                }
                ];
            }

            $columns[] = [
                'class'=>\common\components\ActionColumn::className(),
                'buttons' => [
                    'view' => function ($url, $model) use ($user) {
                        if (!$user->can('menuOffersEdit')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'lead-view'),
                        ]);
                    },
                    
                    'update' => function ($url, $model) use ($user) {
                        if (!$user->can('menuOffersEdit')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'lead-update'),
                        ]);
                    },
                    'delete' => function ($url, $model) use($user) {
                        if (!$user->can('menuOffersDelete')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('app', 'lead-delete'),
                        ]);
                    }
                ],
            ];

        ?>
    <?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',

            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'showPageSummary' => true,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap',

            ],
            'id'=>'offers-grid',

        
            'toolbar' => [
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,        
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-offers'],
        'columns' => $columns,
    ]); ?>
        </div>
    </div>
</div>
<?php
$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}
');

$this->registerJs('

$(".value-div").hide();
$("."+$(".changeSection").val()+"-div").show();
$(".changeSection").change(function(){
    $(".value-div").hide();
    $("."+$(this).val()+"-div").show();
    sumTable();
});

function sumTable(){
    
    var keys = $("#offers-grid").yiiGridView("getSelectedRows");
    var totals = [0,0,0,0];
    
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
                val = val.replace(/[^0-9.,]+/ig, "");
                val = val.replace(",", ".");
                totals[i] += parseFloat(val);
            }
            
        });
    });
    
    var x = 4;
    var y = 7;
    totals[2] = totals[0]-totals[1];
    labels = [];
    labels[0] = "Wartość";
    labels[1] = "Koszt";
    labels[2] = "Zysk";
    for(var j=x;j<y; j++) {

        $(".kv-page-summary td").eq(j).html(labels[j-x]+": "+numberWithCommas(totals[j-x].toFixed(2)));
        $(".kv-page-summary td").eq(j).css("white-space", "nowrap");
    }
    
}

sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});

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

    $(".download-all").click(function(e){
        e.preventDefault();
        downloadAll();
    });
'); 
?>

<script type="text/javascript">
    function downloadAll() {
      var link = document.createElement('a');

      link.setAttribute('download', null);
      link.style.display = 'none';

      document.body.appendChild(link);
      var keys = $("#offers-grid").yiiGridView("getSelectedRows");
      for (var i = 0; i < keys.length; i++) {
        link.setAttribute('href', "<?=yii\helpers\Url::to(['/offer/default/pdf'])?>?id="+keys[i]);
        link.click();
      }

      document.body.removeChild(link);
    }
</script>