<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;
use kartik\helpers\Enum;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">

    <h1><?= Html::encode($this->title) ?></h1>
        <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Stwórz zamówienie'), '#', ['class' => 'btn btn-success', 'onclick'=>'createOrder(); return false;']) ?>
    </p>
    <?php 
    $gridColumn = [
        [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true,
        ],
        [
                'label' => Yii::t('app', 'Nazwa'),
                'attribute'=>'outer_gear_name',
                'value' => function($model){
                        return $model->outerGear->getName()." ".$model->outerGear->outer_gear_model_id;
                },
            ],
        [
                'label' => Yii::t('app', 'Sekcja'),
                'attribute'=>'gear_category_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\GearCategory::getMainList(false),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-gear_category_id2'],
                
                'value' => function($model){
                        return $model->outerGear->outerGearModel->getMainCategory()->name;
                },
            ],
                [
                'attribute' => 'company',
                'label' => Yii::t('app', 'Firma'),
                'value' => function($model){
                    if ($model->outerGear->company)
                    {return $model->outerGear->company->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-gear-search-company_id']
            ],
                [
                'attribute' => 'event_id',
                'label' => Yii::t('app', 'Wydarzenie'),
                'value' => function($model){
                    if ($model->event->name)
                    {return $model->event->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\Event::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-gear-search-event_id']
            ],
        'quantity',
                    [
                        'label' => Yii::t('app', 'Data odbioru'),
                        'attribute'=>'reception_time',
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) {
                            return [
                            'name'=>'reception_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->outerGear->id, 'event_id'=>$gear->event_id],
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
                        'value' => function($gear) {
                            if ($gear->reception_time)
                                return substr($gear->reception_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Data zwrotu'),
                        'attribute'=>'return_time',
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) {
                            return [
                            'name'=>'return_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->outerGear->id, 'event_id'=>$gear->event_id],
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
                        'value' => function($gear) {
                            if ($gear->return_time)
                                return substr($gear->return_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Uwagi'),
                        'attribute'=>'description',
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) {
                            return [
                            'name'=>'description',
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->outerGear->id, 'event_id'=>$gear->event_id],
                                    ],
                                'options' => [

                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) {
                            if ($gear->description)
                                return $gear->description;
                            else 
                                return "-";
                        }
                    ],
                    [
                    'label' => Yii::t('app', 'Odpowiedzialny'),
                    'attribute'=>'user_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\User::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...')],                    
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'editableOptions' => function ($gear, $key, $index)  {
                        return [
                            'inputType' => Editable::INPUT_SELECT2, 
                            'name'=>'user_id',
                            'formOptions' => [
                                    'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->outerGear->id, 'event_id'=>$gear->event_id],
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
                        'value' => function($gear) {
                            if ($gear->user_id)
                                return $gear->user->displayLabel;
                            else 
                                return "-";
                        }
                    ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'filterSelector'=>'.grid-filters',
        'pjax' => false,
        'toolbar' => [
                [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')+1), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
                '{export}',

                ],
        'id'=>'orderGear'
    ]); ?>

</div>
<?php if (Yii::$app->session->getFlash('error')) { $this->registerJs('$( document ).ready(function() {
            toastr.error("'.Yii::$app->session->getFlash('error').'");
        });'); } ?>
