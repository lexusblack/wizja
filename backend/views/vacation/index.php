<?php

use kartik\helpers\Html;
use common\components\grid\GridView;
use common\models\User;
use common\models\Vacation;
use kop\y2sp\ScrollPager;
/* @var $this yii\web\View */
/* @var $searchModel common\models\VacationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Urlopy');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="vacation-index">

    <p>
        <?php if ($user->can('eventVacationsAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'pager' => [
            'class'     => ScrollPager::className(),
            'container' => '.grid-view tbody',
            'item'      => 'tr',
            'paginationSelector' => '.grid-view .pagination',
            'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            'enabledExtensions'  => [
                ScrollPager::EXTENSION_SPINNER,
                //ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'user_id',
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data'=>User::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...')
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'value'=>'user.displayLabel',
            ],
            [
                'attribute' => 'start_date',
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions'=> [
                        'format' => 'yyyy-mm-dd'
                    ]
                ],
            ],
            [
                'attribute' => 'end_date',
                'filterType'=>GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions'=> [
                        'format' => 'yyyy-mm-dd'
                    ]
                ],
            ],
            [
                'attribute' => 'status',
                'value'=>'statusLabel',
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data'=>Vacation::getStatusList(true),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...')
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) use ($user) {
                        if (!$user->can('eventVacationsView')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'lead-view'),
                        ]);
                    },
                    'update' => function ($url, $model) use ($user) {
                        if (!$user->can('eventVacationsEdit')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'lead-update'),
                        ]);
                    },
                    'delete' => function ($url, $model) use($user) {
                        if (!$user->can('eventVacationsDelete')) { return null; }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('app', 'lead-delete'),
                                        'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs('


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');