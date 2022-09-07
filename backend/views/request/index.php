<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use yii\bootstrap\Modal;
use kartik\editable\Editable;
    if (Yii::$app->params['companyID']=="admin")
        {
            $admin = true;
        }else{
            $admin = false;
        }
/* @var $this yii\web\View */
/* @var $searchModel common\models\AddonRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pomoc techniczna - zgłoszenia');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

Modal::begin([
    'id' => 'request-notes',
    'header' => Yii::t('app', 'Historia zgłoszenia'),
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
    'id' => 'request-add',
    'header' => Yii::t('app', 'Podepnij do wydarzenia'),
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
?>
<div class="addon-rate-index">
<p><?php echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj zgłoszenie'), ['/site/send-mail'], ['class' => 'btn btn-success']); ?></p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            'id',
            [
                'attribute'=>'name',
                'format'=>'raw',
                'value'=> function($model)
                {
                    $style = "";
                    if (!$model->isRead())
                        $style = " not-read";
                    return Html::a('<span class="label label-info"><i class="fa fa-comments"></i> '.count($model->requestNotes).'</span> '.$model->name, ['show-notes', 'id'=>$model->id], ['class'=>'show-notes'.$style]);
                }
            ],
            [
                'attribute'=>'company_id',
                'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Company::getList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                'visible'=>$admin,
                'value'=>   function($model)
                {
                    return $model->company->name;
                }
            ],
            'username',
            'create_time',
            'update_time',
            [
                    'attribute'=>'priority',
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Request::getPriorityList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'value' => function($model, $key, $index, $column)
                    {
                        return \common\models\Request::getPriorityList()[$model->priority];
                    },
                ],
            [
                    'attribute'=>'status',
                    'format' => 'html',
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Request::getStatusList(),
                    'visible'=>$admin,
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/request/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\Request::getStatusList($model->status),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'value' => function($model, $key, $index, $column)
                    {
                        return \common\models\Request::getStatusList()[$model->status];
                    },
            ],
            [
                    'attribute'=>'status',
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Request::getStatusList(),
                    'visible'=>!$admin,
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'value' => function($model, $key, $index, $column)
                    {
                        return \common\models\Request::getStatusList()[$model->status];
                    },
            ],
            [
                    'format' => 'html',
                    'visible'=>$admin,
                    'label'=>Yii::t('app', 'Historia'),
                    'value' => function($model, $key, $index, $column)
                    {
                        $content = "";
                        foreach ($model->getHistory() as $h)
                        {
                            $content .=$h->user->displayLabel.Yii::t('app', ' na ').\common\models\Request::getStatusList()[$h->status]."<br/>";
                        }
                        return $content;
                    },
            ],
            [
                    'attribute'=>'type',
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Request::getTypeList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'value' => function($model, $key, $index, $column)
                    {
                        return \common\models\Request::getTypeList()[$model->type];
                    },
            ],
            [
                'label'=>Yii::t('app', 'Zadanie'),
                'format'=>'raw',
                'visible'=>$admin,
                'value'=>function($model){
                    $content = "";
                    foreach ($model->events as $event)
                    {
                        $content.= Html::a($event->name, ['/event/view', 'id'=>$event->id])."<br/>";
                    }
                        $content.= Html::a(Yii::t('app', 'Nowe zadanie'), ['/request/create-from-request', 'id'=>$model->id], ['class'=>'btn btn-success btn-xs']);
                        $content.=" ".Html::a(Yii::t('app', 'Podepnij'), ['/request/add-to-event', 'id'=>$model->id], ['class'=>'btn btn-primary btn-xs add-to-event']);
                    return $content;
                }
            ]
        ],
    ]); ?>
        </div>
    </div>
</div>

<?php

$this->registerJs('
    $(".show-notes").click(function(e){
        $(this).removeClass("not-read");
        $("#request-notes").find(".modalContent").empty();
        e.preventDefault();
        $("#request-notes").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".show-notes").on("contextmenu",function(){
       return false;
    });
');

$this->registerJs('
    $(".add-to-event").click(function(e){
        $("#request-add").find(".modalContent").empty();
        e.preventDefault();
        $("#request-add").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".add-to-event").on("contextmenu",function(){
       return false;
    });
');

$this->registerCss('.not-read{font-weight:bold;}');