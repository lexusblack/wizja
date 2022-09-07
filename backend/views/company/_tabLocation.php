<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Informacje'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedLocations(),
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value'=>function($model)
                    {
                        return Html::a($model->name, ['gear-item/view', 'id'=>$model->id], ['target' => '_blank']);
                    },
                ],
            [
                'label'=>Yii::t('app', 'Publiczne'),
                'attribute'=>'public',
                'format'=>'html',
                'value'=>function ($model, $key, $index, $column) {
                    if ($model->public==0)
                    {
                        return Yii::t('app', "Prywatne");
                    }
                    if ($model->public==1)
                    {
                        return Yii::t('app', "Publiczne (niezaakceptowane)");
                    }
                    if ($model->public==2)
                    {
                        return Yii::t('app', "Publiczne");
                    }
                },
                'contentOptions'=>['class'=>'text-center'],
                'width'=>'70px'
            ],
	            'city',
	            [
	                'value'=>'province.name',
	                'attribute'=>'province_id',
	            ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'location',
                    'contentOptions' => ['style' => 'width:100px;'],
                ],
            ],
        ]);
        ?>
            </div>
        </div>
    </div>
</div>
</div>