<?php
use backend\modules\offers\models\OfferExtraItem;
use common\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\editable\Editable;
$formatter = Yii::$app->formatter;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Oferty'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($user->can('eventsEventEditEyeOfferAdd')) {
            echo Html::a(Yii::t('app', 'Dodaj nową'), ['/agency-offer/create', 'event_id' => $model->id], ['class' => 'btn btn-success']);
        }
        if ($user->can('eventsEventEditEyeOfferImport')) {
            echo Html::a(Yii::t('app', 'Importuj z ofert'), ['/agency-offer/assign-to-event', 'event_id' => $model->id], ['class' => 'btn btn-success']);
        }
         ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
                <?php
            $assignedOffers = $model->getAssignedAgencyOffers(); 
            $columns = [
                [
                    'attribute'=>'name',
                    'value' => function($model, $key, $index, $column)
                    {
                        return Html::a( $model->name, Url::to(['/agency-offer/view', 'id'=>$model->id]));
                    },
                    'format'=>'html',
                ],
                'offer_date',
                [
                    'label'=>Yii::t('app', 'Przygotował'),
                    'attribute'=>'manager_id',
                    'value' => function($model, $key, $index, $column)
                    {
                        $list = \common\models\User::getList();
                        if ($model->manager_id == null) {
                            return Yii::t('app', 'Nikt');
                        }
                        return $list[$model->manager_id];
                    },
                ],
                [
                    'attribute'=>'status',
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/agency-offer/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\AgencyOffer::getStatusList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                    'value' => function($model, $key, $index, $column)
                    {
                        
                        $list = \common\models\AgencyOffer::getStatusList();
                        //return  $form->field($model, 'status')->dropDownList($list);

                        return $list[$model->status];
                    },
                ],
                [
                    'label'=>Yii::t('app', 'Wartość netto'),
                    'value' => function($model, $key, $index, $column) use ($formatter)
                    {
                        return $formatter->asCurrency($model->getNettoValue());
                    },                    
                ],
                [
                    'label'=>Yii::t('app', 'Zysk agencji netto'),
                    'value' => function($model, $key, $index, $column) use ($formatter)
                    {
                        return $formatter->asCurrency($model->getProfitValue());
                    },                    
                ]
            ];
            if ($user->can('eventsEventEditEyeOfferDelete')) {
                $columns[] = [
                    'value' => function($model) {
                        return Html::a(Html::icon('remove'), ['/agency-offer/offer-event', 'event_id'=>$model->event_id], [ 'class'=>'btn btn-danger btn-sm delete-from-event','data' => ['id' => $model->id]]);
                    },
                    'format' => 'raw',
                ];
            }
            echo GridView::widget([
                'layout' => "{items}\n{pager}",
                'dataProvider'=>$assignedOffers,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => $columns,
                ]); ?>
        </div>
    </div>
</div>

<?php $this->registerJs('

    $(".delete-from-event").on("click",function(){
            var _this = $(this),
            data = {
                itemId: _this.data("id"),
                add: 0
            };
            $.post(_this.attr("href"), data, function(response){
                location.reload();
            });
        

        return false;
    });
');?>