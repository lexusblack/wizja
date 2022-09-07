<?php
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<h3><?= Yii::t('app', 'Załączniki') ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a('Dodaj', ['gear-attachment/create', 'gearId'=>$model->id], ['class'=>'btn btn-success']); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedAttachements(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'value'=>function($model)
                    {
                        return Html::a($model->filename, ['gear-attachment/download', 'id'=>$model->id]);
                    },
                    'format' => 'html',
                ],
//                'typeLabel:text:Typ'
            ],
        ]);
        ?>
    </div>
</div>