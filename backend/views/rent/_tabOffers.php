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
<h3><?php echo Yii::t('app', 'Oferty'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php if ($user->can('eventRentsOffer')): ?>
        <?php echo Html::a(Yii::t('app', 'Dodaj nową'), ['/offer/default/create', 'rent_id'=>$model->id], ['class'=>'btn btn-success']); 
        if ($model->customer_id)
           echo Html::a(Yii::t('app', 'Stwórz z wypożyczenia'), ['/offer/default/create-from-rent', 'rent_id' => $model->id], ['class' => 'btn btn-success']);
       else
            echo Html::a(Yii::t('app', 'Stwórz z wypożyczenia'), ['#'], ['class' => 'btn btn-success', 'onclick'=>'alert("Nie można stworzyć oferty, najpierw wypełnij w wypożyczeniu pole klient"); return false;']);
             ?>

    <?php endif; ?>
    <?php if ($user->can('eventRentsOffer')): ?>
        <?php echo Html::a(Yii::t('app', 'Importuj z ofert'), ['/offer/default/assign-to-rent', 'rent_id'=>$model->id], ['class'=>'btn btn-success']); ?>
<?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
            $assignedOffers = $model->getAssignedOffers(); 
            $offers = $assignedOffers->getModels();
            $gcat = \common\models\GearCategory::getMainList(true);
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
                    'value' => function($model, $key, $index, $column) use ($user)
                    {
                        if ($user->can('menuOffersEdit'))
                            return Html::a( $model->name, Url::to(['/offer/default/view', 'id'=>$model->id]));
                        else
                            return Html::a( $model->name, Url::to(['/offer/default/pdf2', 'id'=>$model->id]));
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
                ]];

            if ($user->can('menuOffersEdit'))
            {
                $columns[] =
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
                                ],
                        'pluginEvents' =>   [ 
                            "editableSuccess"=>"function(event, val, form, data) { location.reload();}",
                        ]
                        ];
                    },
                    'value' => function($model, $key, $index, $column)
                    {
                        
                        $list = \common\models\Offer::getStatusList();
                        //return  $form->field($model, 'status')->dropDownList($list);

                        return $list[$model->status];
                    },
                ];
            }else{
                $columns[] =
                [
                    'attribute'=>'status',
                    'value' => function($model, $key, $index, $column)
                    {
                        
                        $list = \common\models\Offer::getStatusList();
                        return $list[$model->status];
                    },
                ];
            }
            if ($user->can('menuOffersEdit')) {
            foreach ($gcat as $key => $cat) {
                $columns[] = [
                    'label'=>$cat->name,
                    'value' => function($model, $key, $index, $column) use ($cat)
                    {
                        $vals = $model->getOfferValues();
                        return isset($vals[$cat->name]) ? Yii::$app->formatter->asCurrency($vals[$cat->name]) : Yii::$app->formatter->asCurrency(0);
                    },
                ];
            }

            $columns[] = [
                'label' => Yii::t('app', 'Transport'),
                'value' => function ($model){
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Transport')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Transport')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label' => Yii::t('app', 'Obsługa'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Obsługa')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Obsługa')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label' => Yii::t('app', 'Inne'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Inne')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Inne')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label'=>Yii::t('app', 'Suma'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Suma')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Suma')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
        }
            if ($user->can('eventsEventEditEyeOfferDelete')) {
                $columns[] = [
                    'value' => function($model) {
                        return Html::a(Html::icon('remove'), ['/offer/default/offer-rent', 'rent_id'=>$model->rent_id], [ 'class'=>'btn btn-danger btn-sm delete-from-event','data' => ['id' => $model->id]]);
                    },
                    'format' => 'raw',
                ];
            }

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
                'afterRow' => function($model, $key, $index, $grid) use ($columns)
                {
                    $content = '';
                    $rowOptions =  [
                        'class'=>'offer-gear-details',
                        'style'=>'display:none',
                    ];

                    // Gear
                    
                    $content .= "<h3>Sprzęt</h3>";
                    $content .= GridView::widget([
                        'layout'=>'{items}',

                        'dataProvider'=>$model->getGearDataProvider(),
                        'options'=>[
                            'class'=>'grid-view grid-view-items',
                        ],
                        'filterModel' => null,
                        'columns' => [
                            [
                                'attribute' => 'photo',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->photo == null)
                                    {
                                        return '-';
                                    }
                                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                                },
                                'format'=>'raw',
                                'contentOptions'=>['class'=>'text-center'],
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function ($model, $key, $index, $column) {
                                    $content = Html::a($model->name, ['gear/update', 'id'=>$model->id]);
                                    return $content;
                                },
                                'format' => 'html',
                            ],
                            [
                                'attribute'=>'quantity',
                                'value'=>function($gear, $key, $index, $column)
                                {
                                    /* @var $gear \common\models\Gear */
                                    if ($gear->no_items==true)
                                    {
                                        return $gear->quantity;
                                    }
                                    else
                                    {
                                        return $gear->getGearItems()->count();
                                    }
                                }
                            ],
                            'brightness:decimal',
                            'power_consumption:decimal',
                            [
                                'attribute'=>'weight',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->weight)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->weight).' kg';
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'width',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->width)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->width).' cm';
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'height',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->height)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->height).' cm';
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'depth',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->depth)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->depth).' cm';
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'volume',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->depth)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->getCalculatedVolume()).' cm<sup>3</sup>';
                                    }
                                    return null;
                                },
                                'format'=>'html',
                            ],
                            'price:currency',
                        ],
                    ]);

                    // end Gear

                    // Outer Gear
                    
                    $content .= '<h3>'.Yii::t('app', 'Sprzęt zewnętrzny').'</h3>';
                    $content .= GridView::widget([
                        'layout'=>'{items}',
                        'dataProvider'=>$model->getOuterGearDataProvider(),
                        'options'=>[
                            'class'=>'grid-view grid-view-items',
                        ],

                        'filterModel' => null,
                        'columns' => [
                            [
                                'attribute' => 'photo',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->photo == null)
                                    {
                                        return '-';
                                    }
                                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                                },
                                'format'=>'raw',
                                'contentOptions'=>['class'=>'text-center'],
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function ($model, $key, $index, $column) {
                                    $content = Html::a($model->name, ['outer-gear/update', 'id'=>$model->id]);
                                    return $content;
                                },
                                'format' => 'html',
                            ],
                            [
                                'attribute'=>'quantity',
                                'value'=>function($gear, $key, $index, $column)
                                {
                                    return $gear->quantity;
                                }
                            ],
                            'brightness:decimal',
                            'power_consumption:decimal',
                            [
                                'attribute'=>'weight',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->weight)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->weight).' '. Yii::t('app', 'kg');
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'width',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->width)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->width).' '. Yii::t('app', 'cm');
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'height',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->height)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->height).' '. Yii::t('app', 'cm');
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'depth',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->depth)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->depth).' '. Yii::t('app', 'cm');
                                    }
                                    return null;
                                }
                            ],
                            [
                                'attribute'=>'volume',
                                'value' => function ($model, $key, $index, $column)
                                {
                                    if ($model->depth)
                                    {
                                        return Yii::$app->formatter->asDecimal($model->getCalculatedVolume()).' '. Yii::t('app', 'cm').'<sup>3</sup>';
                                    }
                                    return null;
                                },
                                'format'=>'html',
                            ],
                            'price:currency',
                        ],
                    ]);

                    //end outer-gear
                    

                    return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($columns)]), $rowOptions);
                },
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
        if (confirm("'.Yii::t('app', 'Czy na pewno chcesz odpiąć tę ofertę?').'")) {
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