<?php

use yii\helpers\Html;
use common\components\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel common\models\UserEventRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title =  Yii::t('app', 'Role na wydarzeniu');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="user-event-role-index">

    <p>
        <?php
        if ($user->can('settingsRoleAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        if ($user->can('settingsAddons')) {
            echo Html::a('<i class="fa fa-users"></i> ' . Yii::t('app', 'Dodatki'), ['/addon-rate/users'], ['class' => 'btn btn-primary']);
        }

        ?>
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
            [
                'attribute'=>'name',
                'format'=>'html',
                'value'=>function($model){
                    return Html::a($model->name, ['view', 'id'=>$model->id]);
                }
            ],
            [
                'attribute'=>'compatibility',
                'value'=>'compatibilityLabel',
                'filter'=>\common\models\UserEventRole::getCompatibilityList(),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('settingsRoleEdit'),
                    'delete'=>$user->can('settingsRoleDelete'),
                    'view'=>$user->can('settingsRoleView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>