<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Firmy'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['outer-gear/create', 'outerGearModelId' => $model->id], ['class' => 'btn btn-success']);
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
        if ($model->type==3)
        {
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedItems(),
            'columns' => [
                                           [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'format' => 'html',
                            'header' => Yii::t('app', 'Dostawca'),
                            'value' => function ($model) {
                                    if($model->company)
                                        return $model->company->name;
                                    else
                                        return "-";
                            },
                        ],
                        'price:currency',
                        'selling_price:currency'
                    ]
        ]);
        }else{
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedItems(),
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
                        'selling_price:currency'
                    ]
        ]);

        }
            ?>
            </div>
        </div>
    </div>
</div>
</div>