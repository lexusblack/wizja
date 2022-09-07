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
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
            [
                'label' => Yii::t('app', 'Zdjęcie'),
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if (!$model->gearModel || $model->gearModel->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->gearModel->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
        [
                'attribute' => 'gear_model_id',
                'label' => Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value' => function($model){
                    if ($model->gearModel)
                    {
                        return $model->gearModel->name;
                    }
                    else
                    {return NULL;}
                }
        ],
        'quantity',
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $model->getAssignedCrossRentals(),
        'columns' => $gridColumn,
    ]); ?>
            </div>
        </div>
    </div>
</div>
</div>