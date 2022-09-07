<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Koszty'); ?></h3>
<div class="row">
<div class="col-lg-12">
        <?php
        echo Html::a(Yii::t('app', 'Dodaj'), ['hall-group-cost/create', 'hall_group_id' => $model->id], ['class' => 'btn btn-success btn-xs'])." ";
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedHallGroupCosts(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute'=>'name',
                ],
                [
                    'attribute'=>'cost',
                ],
                                [
                    'attribute'=>'currency',
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'template'=>'{update}{delete}',
                    'controllerId'=>'hall-group-cost',
                ]
            ],
        ]);
        ?>
                                </div>
</div>
</div>
