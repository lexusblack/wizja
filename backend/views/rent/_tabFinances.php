<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\web\View;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Finanse'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php if (Yii::$app->user->can('eventsEventEditEyeFinanceAddInvoice')): ?>
            <?php echo Html::a(Yii::t('app', 'Wystaw fakturę'), ['/finances/invoice/create', 'id'=>$model->id, 'owner'=>\common\models\Invoice::OWNER_TYPE_RENT], ['class'=>'btn btn-default', 'target'=>'_blank']); ?>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
                 <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                    <h5><?php echo Yii::t('app', 'Faktury przychody'); ?></h5>
                </div>
                <div class="ibox-content">
<?php
                echo GridView::widget([
                    'dataProvider'=>$model->getInvoicesDataProvider(),
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'attribute'=>'fullnumber',
                            'value' => function($model)
                            {
                                $content = Html::a($model->fullnumber, ['/finances/invoice/view', 'id'=>$model->id], ['target'=>'_blank']);
                                return $content;
                            },
                            'format'=>'raw'
                        ],

                        [
                            'attribute'=>'customer_id',
                            'value' => function ($model)
                            {
                                $label = '';
                                if ($model->customer)
                                {
                                    $label = $model->customer->displayLabel;
                                }
                                return $label;
                            },
                            'filter'=>\common\models\Customer::getList(),
                            'filterType'=>GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...')
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                ],
                            ],
                        ],
                        [
                            'class'=>\common\components\grid\LabelColumn::className(),
                            'attribute'=>'type',
                            'filter'=>\common\models\Invoice::getTypeList(),
                        ],
                        [
                            'attribute'=>'date',
                            'filterType'=>GridView::FILTER_DATE_RANGE,
                            'filterWidgetOptions' => [
                                'pluginOptions'=> [
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ],
                        ],
                        'paymentdate',
                        [
                            'attribute'=>'alreadypaid',
                            'pageSummary'=>true,
                            'format' => 'currency',
                            'contentOptions'=>[
                                'class'=>'sum-cell',
                            ]
                        ],
                        [
                            'attribute'=>'total',
                            'pageSummary'=>true,
                            'format' => 'currency',
                            'contentOptions'=>[
                                'class'=>'sum-cell',
                            ]
                        ],
                    ],
                ]);
                ?>
    </div>
</div>
</div>
</div>
</div>
