<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Zakupy'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['gear-purchase/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
        }
        ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getPurchaseGears(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute'=> 'datetime',
                    'value'=> function($model)
                    {
                        return substr($model->datetime, 0, 10);
                    }
                ],
                [                
                    'attribute' => 'customer_id',
                    'label' => Yii::t('app', 'Kontrahent'),
                    'format' => 'html',
                    'value'=>function($model)
                    {
                            return Html::a($model->customer->name, ['customer/view', 'id' => $model->customer_id]);

                    },
                ],
                'price',
                [
                    'attribute' => 'quantity',
                    'label' => Yii::t('app', 'Liczba sztuk'),
                    'value'=>function($model)
                    {
                           return $model->quantity;

                    },                    
                ],
                'total_price',
                [
                    'attribute' => 'expense_id',
                    'label' => Yii::t('app', 'FV'),
                    'value'=>function($model)
                    {
                           if (isset($model->expense))
                            return $model->expense->number;
                        else
                            return "-";

                    },                    
                ],
                [
                    'attribute' => 'user_id',
                    'label' => Yii::t('app', 'Dodał'),
                    'value'=>function($model)
                    {
                           return $model->user->displayLabel;

                    },                    
                ],                
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-purchase',
                    'visibleButtons' => [
                        'update'=>true,
                        'delete'=>true,
                        'view'=>false,
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