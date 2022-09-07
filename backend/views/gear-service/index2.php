<?php

use kartik\helpers\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use kartik\helpers\Enum;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Serwis');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-service-index">
<div class="row">
<div class="col-lg-12">
<?= $this->render('_statutMenu', ['statut'=>$statut, 'params'=>$params]) ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.grid-filters',
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'toolbar' => [
                [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
                '{export}'
                ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'gear_item_name',
                'label'=>Yii::t('app', 'Nazwa sprzętu'),
                'format'=>'html',
                'value'=>
                function ($gear) {
                            if ($gear->gearItem->gear->no_items)
                                return Html::a($gear->gearItem->gear->name. " [" . $gear->quantity."]",['/gear-service/view', 'id'=>$gear->id]);
                            else    
                                return Html::a($gear->gearItem->name. " [" . $gear->gearItem->number."]",['/gear-service/view', 'id'=>$gear->id]);
                        },
            ],
             [
            'attribute'=>'category_id',
            'label'=>Yii::t('app', 'Sekcja'),
            'value'=>function($gear)
            {
                return $gear->gearItem->gear->category->name;
            },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\GearCategory:: getFullList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Kategoria'), 'id' => 'grid-gear-model-search-category_id']
            ],           
            [
            'attribute'=>'description',
            'format'=>'raw'
            ],
            [
            'label'=>Yii::t('app', 'Historia'),
            'format'=>'html',
            'attribute'=>'history',
            'value'=>function($service){
                $return = "";
                $first = true;
                foreach($service->getHistory() as $h)
                {
                    $return .= $h->user->displayLabel.": ".\common\models\GearService::getStatusList()[$h->statut_to]." (".$h->datetime.")<br/>";
                    if (($first)&&(count($service->getHistory())>1))
                    {
                        $return .="<div class='display_none' style='display:none'>";
                        $first = false;
                    }
                }
                if (count($service->getHistory())>1)
                {
                    $return.="</div>";
                    $return .="<a href='#' class='not-show'>".Yii::t('app', 'pokaż więcej')."</a>";
                }
                
                return $return;
             }
            ],
            
            [
            'label'=>Yii::t('app', 'Priorytet'),
            'attribute'=>'priority',
            'format'=>'html',
            'class'=>\kartik\grid\EditableColumn::className(),
            'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/gear-service/priority', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\GearService::getPriorityList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
            'value'=>function($service){
                if ($service->priority)
                    return \common\models\GearService::getPriorityList()[$service->priority];
                else
                    return "-";
             }
            ],
            [
                'attribute'=>'create_time',
                'value'=>function($model){
                    return substr($model->create_time, 0, 16);
                }
            ],
            [
                'attribute'=>'update_time',
                'value'=>function($model){
                    return substr($model->update_time, 0, 16);
                }
            ],
            [
                'attribute'=>'deadline',
                'value'=>function($model){
                    return substr($model->deadline, 0, 16);
                }
            ],
            [
                'label' => Yii::t('app', 'Czas'),
                'value'=>function($service){
                    $time = $service->create_time;
                    foreach($service->getHistory() as $h)
                    {
                        if ($h->statut_to!=$service->status)
                            $time = $h->datetime;
                    }
                    $datetime1 = date_create($time); 
                    $datetime2 = date_create($service->update_time); 
                      
                    // calculates the difference between DateTime objects 
                    $interval = date_diff($datetime1, $datetime2); 
                      
                    // printing result in days format 
                    if ($interval->d>0)
                        return $interval->format('%dd %hh');
                    else    
                        return $interval->format('%hh %im');
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ],
            [
                'label' => Yii::t('app', 'W konfliktach'),
                'format'=>'raw',
                'value' => function ($service){
                    $ids = ArrayHelper::map(\common\models\Event::find()->andWhere(['>', 'event_start', date("Y-m-d")])->asArray()->all(), 'id', 'id');
                    $conflicts = \common\models\EventConflict::find()->where(['gear_id'=>$service->gearItem->gear_id])->andWhere(['resolved'=>0])->andWhere(['event_id'=>$ids])->count();
                    if ($conflicts)
                        $content= Html::tag('span', $conflicts, ['class' => 'label label-danger']);
                    else
                        $content= Html::tag('span', $conflicts, ['class' => 'label label-primary']);
                    return $content;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('gearServiceUpdate'),
                    'delete'=>false,
                    'view'=>$user->can('gearServiceView'),
                ]
            ],
        ],
    ]); ?>
 </div>
</div>
</div>

<?php
$this->registerJs('

$(".not-show").click(function(e){
    e.preventDefault();
    var ourDiv = $(this).parent().find("div").first();
    if (ourDiv.hasClass("display_none")) {
        ourDiv.slideDown();
    }
    else {
        ourDiv.slideUp();
    }
    ourDiv.toggleClass("display_none");
});

$(".table-bordered").each(function(){
$(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
$(this).removeClass("table-striped");
});


');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');