<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SkillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Umiejętności');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="skill-index">

    <p><?php
        if ($user->can('usersSkillsCreate')) {
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
            'name',
            'description:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('usersSkillsEdit'),
                    'delete'=>$user->can('usersSkillsDelete'),
                    'view'=>$user->can('usersSkillsView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>