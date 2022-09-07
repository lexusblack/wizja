<?php
use common\models\EventOuterGear;
use common\models\OutcomesGearOuter;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
use yii\bootstrap\Modal;
Modal::begin([
    'header' => (Yii::$app->session->get('company')==1) ? "<h4 class='modal-title'>". Yii::t('app', 'Zarządzaj sprzętem zewn.')."</h4>"  : "<h4 class='modal-title'>". Yii::t('app', 'Zamów usługi')."</h4>",
    'id' => 'outer_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
 ?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Stwórz zamówienie'), '#', ['class' => 'btn btn-success', 'onclick'=>'createOrder(); return false;']) ?>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h5><?php  echo  (Yii::$app->session->get('company')==1) ? Yii::t('app', 'Sprzęt zarezerwowany u wypożyczającego') : Yii::t('app', 'Usługi zarezerwowane'); ?></h5>
        <?php /*
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
            ]) */
        ?>

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
                        'label' => Yii::t('app', 'Zarezerwowane'),
                        'format'=>'raw',
                        'attribute'=>'quantity',
                        'value' => function($gear) use ($model) {
                            $return = '';
                            $return = $gear->quantity;
                            $gear_one = $model->getEventOuterGearModels()->where(['outer_gear_model_id'=>$gear->outerGear->outer_gear_model_id])->one();
                            return $return." ".Yii::t('app', 'szt.')." <br/><a href='#' onclick='openGearModal(".$gear_one->id."); return false;'>".Yii::t('app', 'Zarządzaj')."</a>";
                        }
                    ], 
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
                        'visible'=>(Yii::$app->session->get('company')==1),
                        'value' => function($gear) {
                            if ($gear->reception_time)
                                return substr($gear->reception_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Data zwrotu'),
                        'visible'=>(Yii::$app->session->get('company')==1),
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
                                            'itemId'=>$item->outer_gear_id,
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
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $model->getEOuterGears(),
        'columns' => $gridColumn,
        'filterSelector'=>'.grid-filters',
        'pjax' => false,
        'id'=>'orderGear'
    ]); ?>
    </div>
</div>
    </div>
</div>

</div>
<?php $spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";
 ?>
<script type="text/javascript">

function openGearModal($id)
{
        var modal = $("#outer_modal");
        var $link="<?=Url::to("/admin/outer-gear-model/manage")?>";
        $link=$link+"?id="+$id;
        modal.modal("show");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("<?=$spinner?>");
        modal.find(".modalContent").load($link); 
}
</script>
<?php
$this->registerJs('

$(".remove-assignment-button-outer").on("click", function(e){
    e.preventDefault();
    $(this).parent().parent().remove();
        
    $.post($(this).prop("href"), $(this).data());
    window.location.reload();
    return false;
});

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');


$this->registerCss('

.row-all-gear-out {
    background-color: #449D44;
    color: white;
}
.row-all-gear-out a {
    color: white;
}
');