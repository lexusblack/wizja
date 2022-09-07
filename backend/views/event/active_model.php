         <?php
                $content = '';
               $content .= GridView::widget([
                   'dataProvider' => $warehouse->gearItemDataProvider,
                   'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
                   'layout' => '{items}',
                   'options'=>[
                        'class'=>'grid-view grid-view-items',
                   ],
                   'rowOptions' => function ($model, $key, $index, $grid)
                   {
                       $options = [];
                        if ($model->group_id != null)
                        {
                            $options['class'] = 'warning';
                        }
                        return $options;

                   },
                    'filterModel' => null,
                    'columns' => [
                        ['class' => 'yii\grid\CheckboxColumn'],
                        //'id',
                        [
                            'header' => Yii::t('app', 'Nazwa'),
                            'format' => 'html',
                            'value' => function ($gear)  {
                                
                                if ($gear->status == GearItem::STATUS_NEED_SERVICE) {
                                    $service = GearService::getCurrentModel($gear->id);
                                    return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]) . " " . Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }
                                if ($gear->status == GearItem::STATUS_SERVICE) {
                                    $service = GearService::getCurrentModel($gear->id);
                                    return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]) . " " . Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }

                                return Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]). " " . Html::a(Yii::t('app', 'Wyślj na serwis'), ['/gear-service/create', 'id'=>$gear->id], ['class'=>'label label-primary']);
                            }
                        ],
                        'number:text:'.Yii::t('app', 'Nr'),
                        //'code:text:'.Yii::t('app', 'Kod'),
                        //'serial:text:'.Yii::t('app', 'Nr seryjny'),
                        'warehouse',
                        [
                            'attribute' => 'location',
                            'label' => Yii::t('app', 'Miejsce w<br/>magazynie'),
                            'encodeLabel'=>false,
                        ],
                        [
                            'header' => Yii::t('app', 'Sprawdzony'),
                            'format' => 'html',
                            'class'=>\kartik\grid\EditableColumn::className(),
                           'value' => function ($gear) {
                                $date = "";
                                if ($gear->test_date)
                                    $date = " (".date("d.m.Y", strtotime($gear->test_date)).")";
                                return $gear->tester.$date;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => Yii::t('app', 'imię i nazwisko sprawdzającego'),
                                    'name'=>'tester',
                                    'formOptions' => [
                                            'action'=>['/gear-item/test', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Godziny lamp'),
                            'format' => 'html',
                            'class'=>\kartik\grid\EditableColumn::className(),
                            'value' => function ($gear) {
                                return $gear->lamp_hours;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'name'=>'lamp_hours',
                                    'header' => Yii::t('app', 'godziny lamp'),
                                    'formOptions' => [
                                            'action'=>['/gear-item/lamps', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Uwagi'),
                            'class'=>\kartik\grid\EditableColumn::className(),
                            'format' => 'html',
                            'value' => function ($gear) {
                                return $gear->info;
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'name'=>'info',
                                    'inputType' => Editable::INPUT_TEXTAREA,
                                    'formOptions' => [
                                            'action'=>['/gear-item/info', 'id'=>$model->id],
                                        ]
                                ];
                            },
                        ],
                        /*[
                            'attribute' => 'purchase_price',
                            'label' => Yii::t('app', 'Cena<br/>zakupu'),
                            'encodeLabel'=>false,
                            'visible'=>Yii::$app->user->can('warehouseGearItemPriceView')
                        ],
                        [
                            'attribute' => 'refund_amount',
                            'label' => Yii::t('app', 'Kwota<br/>zwrotu'),
                            'encodeLabel'=>false,
                            'visible'=>Yii::$app->user->can('warehouseGearItemPriceView')
                        ],*/
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visibleButtons' => [
                                'update'=>Yii::$app->user->can('gearItemEdit'),
                                'delete'=>Yii::$app->user->can('gearItemDelete'),
                                'view'=>Yii::$app->user->can('gearItemView'),
                            ],
                            'urlCreator' =>  function($action, $model, $key, $index)
                            {
                                $params = is_array($key) ? $key : ['id' => (string) $key];
                                $params[0] = 'gear-item/' . $action;

                                return Url::toRoute($params);
                            },
                            'template' => '{history} {view} {update} {delete} {service}',
                        ],
                    ],
                ]);
                $content = $this->render('_group', ['checkbox'=>false, 'warehouse'=>$warehouse, 'gearColumns'=>$gearColumns]).$content;
                $content.= Html::a(Yii::t('app', 'Utwórz case'), ['group-create'], ['class'=>'btn btn-success btn-xs group-create', 'data-pjax'=>0]);
                $content = Html::tag('div', $content, ['class'=>'wrapper']);
echo $content;