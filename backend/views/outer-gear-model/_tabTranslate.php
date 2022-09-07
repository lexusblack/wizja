<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Tłumaczenia'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj tłumaczenie'), ['outer-gear-translate/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
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
            'dataProvider'=>$model->getTranslates(),
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    [
                            'attribute'=>'language_id',
                            'value'=>function($model){
                                return $model->language->name;
                            }
                    ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'outer-gear-translate',
                    'visibleButtons' => [
                        'update'=>$user->can('gearCreate'),
                        'delete'=>$user->can('gearCreate'),
                        'view'=>false
                    ]
                ],
                ]
                ]);
        ?>
            </div>
        </div>
    </div>
</div>

</div>