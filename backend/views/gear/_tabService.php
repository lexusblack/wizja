<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Akcje serwisowe'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">

        <?= GridView::widget([
            'dataProvider' => $serviceDataProvider,
            'filterModel' => $serviceSearchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute'=>'status',
                    'value'=>'statusLabel',
                    'filter'=>\common\models\GearService::getStatusList(),
                ],
                'description:html',
                'create_time',
                [
                    'label'=>Yii::t('app', 'Historia'),
                    'format'=>'raw',
                    'value'=>function($model){
                        $content = "";
                        foreach (\common\models\GearServiceHistory::find()->where(['gear_service_id'=>$model->id])->all() as $h)
                        {
                            $content .= $h->statutTo->name." [".$h->user->displayLabel."] ".substr($h->datetime, 0, 16)."<br/>";
                        }
                        return $content;
                    }
                ],
                ['class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-service',
                ],
            ],
        ]); ?>
            </div>
        </div>
    </div>
</div>
</div>