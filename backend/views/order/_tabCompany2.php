<?php
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;
use kartik\helpers\Enum;
use kartik\tabs\TabsX;
use yii\helpers\Html;


/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">

    <h1><?= Html::encode($this->title) ?></h1>
        <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Stwórz listę'), '#', ['class' => 'btn btn-success', 'onclick'=>'createPurchase(); return false;']) ?>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj do listy'), '#', ['class' => 'btn btn-success', 'onclick'=>'addPurchase(); return false;']) ?>
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
        'dataProvider' => $dataProvider2,
        'columns' => $gridColumn,
        'filterSelector'=>'.grid-filters',
        'pjax' => false,
        'id'=>'createPurchaseList'
    ]); ?>

</div>
<?php if (Yii::$app->session->getFlash('error')) { $this->registerJs('$( document ).ready(function() {
            toastr.error("'.Yii::$app->session->getFlash('error').'");
        });'); } ?>


        
<script type="text/javascript">
    function createPurchase()
        {
           var keys = $("#createPurchaseList").yiiGridView("getSelectedRows");
           console.log(keys);
           var json = JSON.stringify(keys)
           location.href = "/admin/purchase-list/create?ids="+json;
        }
    function addPurchase()
        {
           var keys = $("#createPurchaseList").yiiGridView("getSelectedRows");
           console.log(keys);
           var json = JSON.stringify(keys)
           location.href = "/admin/purchase-list/add?ids="+json;
        }
</script>