<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Ceny'); ?></h3>
<div class="row">
<div class="col-lg-12">
        <?php
        echo Html::a(Yii::t('app', 'Dodaj'), ['hall-group-price/create', 'hall_group_id' => $model->id], ['class' => 'btn btn-success btn-xs'])." ";
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedHallGroupPrices(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute'=>'name',
                    'value'=>function($model){
                    	$name = $model->name;
                    	if ($model->default)
                    	{
                    		$name .=" (".Yii::t('app', 'domyślna').")";
                    	}
                    	return $name;
                    }
                ],
                [
                    'attribute'=>'price',
                ],
                [
                    'attribute'=>'currency',
                ],
                [
                		'label'=>Yii::t('app', '% dnia pierwszego'),
                		'format'=>'raw',
                		'value'=>function($model){
                			return $model->getPercentes();
                		}
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'template'=>'{update}{delete}',
                    'controllerId'=>'hall-group-price',
                ]
            ],
        ]);
        ?>
                                </div>
</div>
</div>
