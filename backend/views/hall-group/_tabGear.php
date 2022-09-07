<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęt'); ?></h3>
<div class="row">
<div class="col-lg-12">
        <?php
        echo Html::a(Yii::t('app', 'Dodaj'), ['hall-group-gear/create', 'hall_group_id' => $model->id], ['class' => 'btn btn-success btn-xs'])." ";
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedHallGroupGears(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'format' => 'raw',
                    'value'=>function($model) use ($user)
                    {
                        return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id], ['target'=>'_blank']);
                    },
                ],
                [
                    'attribute'=>'quantity',
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'template'=>'{update}{delete}',
                    'controllerId'=>'hall-group-gear',
                ]
            ],
        ]);
        ?>
                                </div>
</div>
</div>
