<?php
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<h3><?= Yii::t('app', 'Naprawy') ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedServices(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                'name',
                'description',
                'price',
                'create_time',
                'end_time'
            ],

        ]);
        ?>
            </div>
        </div>
    </div>
</div>