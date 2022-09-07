<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Oddziały');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="department-index">

    <p>
        <?php if ($user->can('settingsCompanyDepartmentsAdd')) {
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

//            'id',
            'name',
            [
                'attribute'=>'color',
                'contentOptions' => function($model, $key, $index, $column)
                {
                    return [
                        'style' => 'background-color:'.$model->color,
                    ];
                }

            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'delete' => $user->can('settingsCompanyDepartmentsDelete'),
                    'update' => $user->can('settingsCompanyDepartmentsEdit'),
                    'view' => $user->can('settingsCompanyDepartmentsView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>