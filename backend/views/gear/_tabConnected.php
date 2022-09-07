<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęty powiązane'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['gear-connected/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
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
            'dataProvider'=>$model->getConnectedGears(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'connected_id',
                    'label' => Yii::t('app', 'Nazwa'),
                    'format' => 'html',
                    'value'=>function($model)
                    {
                            return Html::a($model->connected->name, ['gear/view', 'id' => $model->connected_id]);

                    },
                ],
                [
                    'attribute' => 'quantity',
                    'label' => Yii::t('app', 'Liczba sztuk'),
                    'value'=>function($model)
                    {
                           return $model->quantity." / ".$model->gear_quantity;

                    },                    
                ],
                [
                    'attribute' => 'checked',
                    'label' => Yii::t('app', 'Domyślnie zaznaczone'),
                    'value'=>function($model)
                    {
                           if ($model->checked==1)
                           {
                            return Yii::t('app', 'Tak');
                           }else{
                            return Yii::t('app', 'Nie');
                           }

                    },
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-connected',
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
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj sprzęt zewnętrzny'), ['gear-outer-connected/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
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
            'dataProvider'=>$model->getOuterConnectedGears(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'connected_id',
                    'label' => Yii::t('app', 'Nazwa'),
                    'format' => 'html',
                    'value'=>function($model)
                    {
                            return Html::a($model->connected->name, ['gear/view', 'id' => $model->connected_id]);

                    },
                ],
                [
                    'attribute' => 'quantity',
                    'label' => Yii::t('app', 'Liczba sztuk'),
                    'value'=>function($model)
                    {
                           return $model->quantity." / ".$model->gear_quantity;

                    },                    
                ],
                [
                    'attribute' => 'checked',
                    'label' => Yii::t('app', 'Domyślnie zaznaczone'),
                    'value'=>function($model)
                    {
                           if ($model->checked==1)
                           {
                            return Yii::t('app', 'Tak');
                           }else{
                            return Yii::t('app', 'Nie');
                           }

                    },
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-outer-connected',
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