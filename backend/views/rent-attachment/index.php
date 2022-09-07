<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Załączniki');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachment-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'base_name',
            'extension',
            'mime_type',
//            'status',
            // 'content:ntext',
            // 'create_time',
            // 'update_time',
            // 'info:ntext',
            [
                'attribute' => 'event_id',
                'filter' => \common\models\Event::getModelList(),
                'value'=>'event.name',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>