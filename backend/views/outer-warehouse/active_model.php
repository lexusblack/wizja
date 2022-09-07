         <?php
$user = Yii::$app->user;

         use common\components\grid\GridView;
use common\models\GearItem;
use common\models\GearService;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\editable\Editable;
use kartik\dynagrid\DynaGrid;
                $content = '';
               $content .=  GridView::widget([
                   'dataProvider' => $itemProvider,
                   'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
                   'layout' => '{items}',
                   'columns' => [
                                           [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'format' => 'html',
                            'header' => Yii::t('app', 'Firma'),
                            'value' => function ($model) {
                                    if($model->company)
                                        return $model->company->name;
                                    else
                                        return "-";
                            },
                        ],
                        'quantity',
                        'price:currency',
                        'selling_price:currency',
                        [
                            'class' => 'yii\grid\ActionColumn',

                            'urlCreator'=>function ($action, $model, $key, $index) {
                                $params = is_array($key) ? $key : ['id' => (string) $key];
                                $params[0] = 'outer-gear/' . $action;

                                return Url::toRoute($params);
                            },
                            'template' => '{view} {update} {delete}',
                            'visibleButtons' => [
                                'view'=>$user->can('outerGearView'),
                                'update'=>$user->can('outerGearUpdate'),
                                'delete'=>$user->can('outerGearDelete'),
                            ],
                        ],
                    ]
                   ]);
echo $content;