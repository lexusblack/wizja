<?php
use backend\modules\offers\models\OfferExtraItem;
use common\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\editable\Editable;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
    <?php
            if ($user->can('eventsEventEditEyeOfferAdd')) {
            echo Html::a(Yii::t('app', 'Dodaj nową'), ['/offer/default/create', 'project_id' => $model->id], ['class' => 'btn btn-success']);
        }
        if ($user->can('eventsEventEditEyeOfferImport')) {
            echo Html::a(Yii::t('app', 'Importuj z ofert'), ['/offer/default/assign-to-project', 'project_id' => $model->id], ['class' => 'btn btn-success']);
        } ?>
        <?php
            $assignedOffers = $model->getAssignedOffers(); 
            $columns = [
                [
                    'label' => Yii::t('app', 'Duplkuj'),
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::a('<i class="fa fa-copy"></i>', ['/offer/default/duplicate', 'id' => $model['id']], ['class'=>'btn btn-warning btn-circle']) ;                  
                    },
                    'visible' => $user->can('menuOffersViewDuplicate')
                ],
                [
                    'attribute'=>'name',
                    'value' => function($model, $key, $index, $column)
                    {
                        return Html::a( $model->name, Url::to(['/offer/default/view', 'id'=>$model->id]));
                    },
                    'format'=>'html',
                ],
                'offer_date',
            [
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
            ],
                [
                    'label'=>Yii::t('app', 'PM'),
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
                                    'action'=>['/offer/default/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\Offer::getStatusList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                    'value' => function($model, $key, $index, $column)
                    {
                        $list = \common\models\Offer::getStatusList();
                        return $list[$model->status];
                    },
                ]
            ];


            ?>

    <div class="panel_mid_blocks">
        <div class="panel_block">
<?php
            echo GridView::widget([
	            'layout' => "{items}\n{pager}",
                'dataProvider'=>$assignedOffers,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => $columns,

            ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>

<?php $this->registerJs('
    $(".sub_block").on("click",function(){
        var _this = $(this),
        icon = _this.find("i"),
        box = _this.closest("tr").next(".offer-gear-details");
        if(_this.hasClass("active")){
            icon.removeClass("glyphicon-arrow-up").addClass("glyphicon-arrow-down");
            _this.removeClass("active");
            box.hide(300);
        } else {
            icon.removeClass("glyphicon-arrow-down").addClass("glyphicon-arrow-up");
            _this.addClass("active");
            box.show(300);
        }

        return false;
    });

    $(".delete-from-event").on("click",function(){
        if (confirm("'.Yii::t('app', 'Po usunięciu wszystkie przypisane do oferty egzemplarzy będą też usunięty').'")) {
            var _this = $(this),
            data = {
                itemId: _this.data("id"),
                add: 0
            };
            $.post(_this.attr("href"), data, function(response){
                location.reload();
            });
        } 

        return false;
    });
');?>