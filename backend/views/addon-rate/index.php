<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AddonRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Dodatki');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="addon-rate-index">
    <p>
        <?php
        if ($user->can('settingsAddons')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        if ($user->can('settingsAddons')) {
            echo Html::a('<i class="fa fa-users"></i> ' . Yii::t('app', 'Przypisz'), ['users'], ['class' => 'btn btn-primary']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'amount:currency',
            'name',
            [
                'attribute'=>'level',
                'filter'=>\common\models\Event::getLevelList(),
            ],
            [
                'attribute'=>'period',
                'filter'=>\common\models\AddonRate::getPeriodList(),
                'value'=>'periodLabel',
            ],
            [
                'label' => Yii::t('app', 'Role'),
                'value' => function ($model) {
                    $roles = null;
                    foreach ($model->getRoles()->all() as $role) {
                        $roles .= $role->name . ', ';
                    }
                    if ($roles) {
	                    return substr( trim( $roles ), 0, - 1 );
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('settingsAddonsRateManageUpdate'),
                    'delete'=>$user->can('settingsAddonsRateManageDelete'),
                    'view'=>$user->can('settingsAddonsRateManageView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>