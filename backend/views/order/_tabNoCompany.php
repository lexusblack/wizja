<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
use yii\bootstrap\Modal;
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Zarządzaj sprzętem zewn.')."</h4>",
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

    <h1><?= Html::encode(Yii::t('app', 'Zapotrzebowanie na sprzęt zewnętrzny')) ?></h1>
    <?php 
    $gridColumn = [
        [
                'label' => Yii::t('app', 'Nazwa'),
                'value' => function($model){
                        return $model->outerGearModel->name;
                },
            ],
                [
                'attribute' => 'event_id',
                'label' => Yii::t('app', 'Wydarzenie'),
                'format'=>'html',
                'value' => function($model){
                    if ($model->event->name)
                    {return html::a($model->event->name, ['/event/view', 'id'=>$model->event->id]);}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-gear-model-search-event_id']
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
                    if (isset($model->outerGearModel))
                        return $model->outerGearModel->getMainCategory()->name;
                    else
                        return "-";
                },
            ],
            [
                'attribute' => 'quantity',
                'label' => Yii::t('app', 'Liczba'),
                'value' => function($model){
                    return $model->quantity;
                },
            ],
                     [
                        'label' => Yii::t('app', 'Zarezerwowane'),
                        'format'=>'raw',
                        'value' => function($gear) {
                            $gear_id = $gear->outerGearModel->getEventOuterGearIds();
                            
                            $gears = $gear->event->getEventOuterGears()->where(['IN', 'outer_gear_id', $gear_id])->all();
                            
                            $return = '';
                            foreach ($gears as $g)
                            {
                                $return .= $g->outerGear->company->name." - ".$g->quantity." ".Yii::t('app', 'szt').".<br/>";
                            }

                            $gear_one = $gear->event->getEventOuterGearModels()->where(['outer_gear_model_id'=>$gear->outer_gear_model_id])->one();
                            return $return."<a href='#' onclick='openGearModal(".$gear_one->id.", ".$gear->event->id."); return false;'>".Yii::t('app', 'Zarządzaj')."</a>";
                        }
                    ],
                     [
                        'label' => Yii::t('app', 'Pozostało'),
                        'format'=>'raw',
                        'value' => function($gear) {
                            $gear_id = $gear->outerGearModel->getEventOuterGearIds();                           
                            $gears = $gear->event->getEventOuterGears()->where(['IN', 'outer_gear_id', $gear_id])->all();                            
                            $return = 0;
                            foreach ($gears as $g)
                            {
                                $return += $g->quantity;
                            }
                            $return = $gear->quantity-$return;
                            if ($return<0)
                            {
                                return 0;
                            }else{
                                return $return;
                            }
                            
                        }
                    ],
        'start_time',
        'end_time'
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider2,
        'filterModel' => $searchModel2,
        'columns' => $gridColumn,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-order-model']],
        'export' => false,
        'id'=>'orderGearModel',
        'rowOptions' => function ($gear, $index, $widget, $grid) {
                           /* $gear_id = $gear->outerGearModel->getEventOuterGearIds();                           
                            $gears = $gear->event->getEventOuterGears()->where(['IN', 'outer_gear_id', $gear_id])->all();                            
                            $return = 0;
                            foreach ($gears as $g)
                            {
                                $return += $g->quantity;
                            }
                            $return = $gear->quantity-$return;
                            if ($return<=0)
                            {
                                return ['class' => 'hidden'];
                            }else{
                                return [];
                            }*/
                         },
    ]); ?>

</div>
<?php if (Yii::$app->session->getFlash('error')) { $this->registerJs('$( document ).ready(function() {
            toastr.error("'.Yii::$app->session->getFlash('error').'");
        });'); } ?>
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