<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearAttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Załączniki');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-attachment-index">

    <p>
        <?php if ($user->can('gearAttachmentsCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'filename',
            'extension',
            [
                'attribute'=>'gear_id',
                'value' => function($model)
                {
                    return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id]);
                },
                'format' => 'html',
                'filter' => \common\models\Gear::getModelList(),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('gearAttachmentsEdit'),
                    'delete'=>$user->can('gearAttachmentsDelete'),
                    'view'=>$user->can('gearAttachmentsView'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>