<?php
use common\models\EventOuterGear;
use common\models\OutcomesGearOuter;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
                <?php

        if ($user->can('eventEventEditEyeOuterGearManage')) {
            echo Html::a(Yii::t('app', 'Zarządzaj'), ['outer-warehouse/assign', 'id' => $model->id, 'type' => 'event'], ['class' => 'btn btn-success']);
        }
        ?>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h5><?php echo (Yii::$app->session->get('company')==1) ? Yii::t('app', 'Zapotrzebowanie na sprzęt zewnętrzny'): Yii::t('app', 'Zapotrzebowanie na usługi');?></h5>

        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedOuterGearModels(),
                'id'=>'orderGearModel',
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    [
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            /* @var $model \common\models\OuterGear */
                            if ($model->photo == null)
                            {
                                return '-';
                            }
                            return Html::a(Html::img($model->getFileThumbUrl(), ['width'=>50]), ['outer-gear-model/view', 'id'=>$model->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'outer_gear_model_id',
                        'label'=>Yii::t('app', 'Nazwa'),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a($model->name, ['outer-gear-model/view', 'id'=>$model->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $model->getEventOuterGearModels()->where(['outer_gear_model_id'=>$gear->id])->one();
                            return $gear_no->quantity;
                        }
                    ],
                     [
                        'label' => Yii::t('app', 'Zarezerwowane'),
                        'format'=>'raw',
                        'value' => function($gear) use ($model) {
                            $gear_id = $gear->getEventOuterGearIds();
                            $gears = $model->getEventOuterGears()->where(['IN', 'outer_gear_id', $gear_id])->all();
                            $return = '';
                            foreach ($gears as $g)
                            {
                                $return .= $g->outerGear->company->name." - ".$g->quantity." ".Yii::t('app', 'szt').".<br/>";
                            }
                            $gear_one = $model->getEventOuterGearModels()->where(['outer_gear_model_id'=>$gear->id])->one();
                            return $return."<a href='#' onclick='openGearModal(".$gear_one->id."); return false;'>".Yii::t('app', 'Zarządzaj')."</a>";
                        }
                    ],                   
                    [
                        'class'=>\common\components\grid\WorkingTimeColumn::className(),
                        'parentModel' => $model,
                        'type'=>'outer_gear_model',
                        'connectionClassName' =>\common\models\EventOuterGearModel::className(),
                        'itemIdAttribute'=>'outer_gear_model_id',
                        'visible' => Yii::$app->user->can('eventEventEditEyeOuterGearEdit'),
                    ],
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'value' => function ($gear) use ($model) {
                            $EventOuterGear = \common\models\EventOuterGearModel::find()->where(['event_id' => $model->id])->andWhere(['outer_gear_model_id' => $gear->id])->one();
                            return $EventOuterGear->start_time . " - " . $EventOuterGear->end_time;
                        },
                        'visible' => !Yii::$app->user->can('eventEventEditEyeOuterGearEdit')
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
                                    $button =  Html::a(Html::icon('remove'), ['/outer-warehouse/assign-outer-gear', 'id'=>$model->id, 'type'=>$model->getClassType()], [
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
            ])
        ?>
    </div>
</div>
    </div>
</div>
</div>

<?php
$this->registerJs('

$(".remove-assignment-button-outer").on("click", function(e){
    e.preventDefault();
    $(this).parent().parent().remove();
        
    $.post($(this).prop("href"), $(this).data());
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