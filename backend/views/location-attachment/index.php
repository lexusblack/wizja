<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LocationAttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Załącznik lokacji');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="location-attachment-index">

    <p>
        <?php if ($user->can('locationAttachmentsAdd')) {
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
                'attribute' => 'type',
                'value' =>'typeLabel',
                'filter'=>\common\models\LocationAttachment::getTypeList(),
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'view' => $user->can('locationAttachmentsView'),
                    'update' => $user->can('locationAttachmentsEdit'),
                    'delete' => $user->can('locationAttachmentsDelete'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>