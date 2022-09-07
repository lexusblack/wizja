<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęty podobne'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['gear-similar/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
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
            'dataProvider'=>$model->getSimilarGears(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'similar_id',
                    'label' => Yii::t('app', 'Nazwa'),
                    'format' => 'html',
                    'value'=>function($model)
                    {
                            return Html::a($model->similar->name, ['gear/view', 'id' => $model->similar_id]);

                    },
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-similar',
                    'visibleButtons' => [
                        'update'=>false,
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