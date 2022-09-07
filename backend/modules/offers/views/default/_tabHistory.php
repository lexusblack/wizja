<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Historia wysyłek'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <table class="table">
            <tr><th><?=Yii::t('app', 'Data')?></th><th><?=Yii::t('app', 'Odbiorcy')?></th><th><?=Yii::t('app', 'Nadawca')?></th><th><?=Yii::t('app', 'Plik')?></th></tr>
            <?php foreach($model->offerSends as $os){ ?>
            <tr>
                <td><?=$os->datetime?></td>
                <td><?=$os->recipient?></td>
                <td><?php if ($os->user_id) echo $os->user->displayLabel; ?></td>
                <td><?php if ($os->filename) echo Html::a($os->filename, Yii::getAlias('@uploads/offer/'.$os->filename)); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
    </div>
</div>
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