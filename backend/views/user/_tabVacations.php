<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Urlopy i prośby urlopowe'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $model->getAssignedVacations(),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=> Yii::t('app', 'Od - do'),
                'contentOptions' => ['style' => 'width:200px;'],
                'content' => function ($model, $index, $row, $grid)
                {
                    $model->prepareDateAttributes();
                    return $model->dateRange;
                }
            ],
            [
                'label'=> Yii::t('app', 'Liczba dni'),
                'content' =>function ($model)
                {
                    if ($model->getDays()==1)
                        return $model->getDays().Yii::t('app'," dzień");
                    else
                        return $model->getDays().Yii::t('app'," dni");
                }
                 
            ],
            [
                'label'=> Yii::t('app', 'Status'),
                'content' =>function ($model)
                {
                    return $model->getStatusLabel();
                }
            ]
        ],
    ]); ?>
            </div>
        </div>
    </div>
</div>
</div>