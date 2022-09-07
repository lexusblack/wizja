<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
use yii\bootstrap\Modal;
Modal::begin([
    'header' =>"<h4 class='modal-title'>". Yii::t('app', 'Rozwiąż konflikt')."</h4>",
    'id' => 'conflict_resolve_modal',
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

    <h1><?= Html::encode(Yii::t('app', 'Konflikty')) ?></h1>
    <?php 
    $gridColumn = [
                    [
                        'value'=>function($model)
                        {
                            return Html::a('<i class="fa fa-calendar"></i>', ['/event/conflict-calendar', 'conflict_id'=>$model->id], ['class'=>"show-calendar btn btn-xs btn-default"]);
                        },
                        'format'=>'html'
                    ],
        [
                'label' => Yii::t('app', 'Nazwa'),
                'value' => function($model){
                        return $model->gear->name;
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
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-gear_category_id'],
                
                'value' => function($model){
                        return $model->gear->getMainCategory()->name;
                },
            ],
                [
                'attribute' => 'event_id',
                'label' => Yii::t('app', 'Wydarzenie'),
                'value' => function($model){
                    if ($model->event->name)
                    {return html::a($model->event->name, ['/event/view', 'id'=>$model->event->id]);}
                    else
                    {return NULL;}
                },
                'format' => 'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-event_id']
            ],
                [
                'label' => Yii::t('app', 'Data'),
                'value' => function($model){
                    if ($model->event->name)
                    {$start = Yii::$app->formatter->asDateTime($model->event->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->event->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;}
                    else
                    {return NULL;}
                },
                'format' => 'html',
                'contentOptions'=>['style'=>'width: 110px;'],
            ],
            [
                'attribute' => 'manager_id',
                'label' => Yii::t('app', 'PM'),
                'value' => function($model){
                    if ($model->event)
                    {if ($model->event->manager){
                        return $model->event->manager->displayLabel;
                    }else{ return NULL;}}
                    else
                    {return NULL;}
                },
                'format' => 'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' =>  \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-manager_id']
            ],
            [
                'attribute' => 'quantity',
                'label' => Yii::t('app', 'Pozostało'),
                'value' => function($model){
                    return $model->quantity;
                },
            ],
            [
                        'attribute' => 'added',
                        'label' => Yii::t('app', 'Zarezerwowane'),
            ],
                    [
                        'label' => Yii::t('app', 'Status'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'format' => 'html',
                        'editableOptions' => function ($model, $key, $index) {
                            return [
                                'inputType' => Editable::INPUT_SELECT2,
                                'name'=>'resolved',
                                'formOptions' => [
                                        'action'=>['/event/resolve-conflict', 'id'=>$model->id],
                                    ],
                                    'options' => [
                                        'data'=>[0=>Yii::t('app', 'Nierozwiązany'), 1=>Yii::t('app', 'Rozwiązany')],
                                        'options'=> [
                                            'multiple'=>false,
                                        ]
                                    ]
                            ];
                        },
                        'value' => function ($model){
                            if ($model->resolved)
                                return Yii::t('app', 'Rozwiązany');
                            else
                                return Yii::t('app', 'Nierozwiązany');
                        }
                    ],
                    [
                        'format'=>'raw',
                        'value'=>function($model){ return Html::a('Rozwiąż', '#', ['class'=>'btn btn-primary btn-xs', 'onclick'=>'openResolveModal('.$model->id.', '.$model->gear->category_id.'); return false;']);}
                    ]
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider3,
        'filterModel' => $searchModel3,
        'columns' => $gridColumn,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-order-model']],
                'afterRow' => function($model, $key, $index, $grid)
                {
                    $content = "<div class='conflict-calendar' style='height:400px'></div>";
                    return Html::tag('tr',Html::tag('td', $content, ['colspan'=>8, 'style'=>"padding:0; background-color:white;"]), ['class'=>'event-task-details']);
                },
        'export' => false,
        'id'=>'eventConflict',
        ]); ?>

</div>
<?php $spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>"; ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function openResolveModal(id, category)
    {
        var modal = $("#conflict_resolve_modal");
        var $link="<?=Url::to(['event/conflict-modal']);?>?conflict="+id+"&c="+category;
        modal.modal("show");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("<?=$spinner?>");
        modal.find(".modalContent").load($link); 
    }
</script>
<?php
$this->registerJs('

$(".show-calendar").click(function(e)
{
    e.preventDefault();

    if ($(this).hasClass("opened"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().show();
        $(this).parent().parent().next().find(".conflict-calendar").empty().load($(this).attr("href"));
    }
    $(this).toggleClass("opened");

});
$(".show-calendar").on("contextmenu",function(){
       return false;
    });
');
?>