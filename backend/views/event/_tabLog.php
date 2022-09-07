<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Historia'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedLogs(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                'content',
                [
                    'value'=>'user.displayLabel',
                    'attribute' => 'user_id',
                ],
                'create_time'
            ],
        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>