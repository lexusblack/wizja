<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;

use common\models\UserDayAmount;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Użytkownicy');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="user-index">

    <p>
        <?php
            if ($user->can('usersUsersCreate')) {
                echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
            }
            if ($active && $user->can('usersUsersInactive')) {
                echo Html::a(Yii::t('app', 'Nieaktywni'), ['inactive'], ['class' => 'btn btn-danger']);
            }
            else if ($user->can('usersUsersInactive')) {
                echo Html::a(Yii::t('app', 'Aktywni'), ['index'], ['class' => 'btn btn-primary']);
            }
        ?>
    </p>
    <?php 
    $groups_super_user = \common\helpers\ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
    $superusers = \common\models\User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>\common\helpers\ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count(); 
    $users = \common\models\User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->count()-$superusers;
            $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
    ?>
    <?php
            if ($user->can('usersUsersCreate')) { ?>
    <h1><?=Yii::t('app', 'Limit kont typu SuperUser: ').$superusers."/".$superuser->superusers_paid?></h1>
    <h3><?=Yii::t('app', 'Limit kont User: ').$users."/".$superuser->users_paid?></h3>
    <?php } ?>
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
            ['class'=>\common\components\grid\PhotoColumn::className()],
            [
                'attribute' => 'username',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->username, ['view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            'first_name',
            'last_name',
            'email:email',
            [
                'label'=>Yii::t('app', 'SuperUser'),
                'attribute' => 'superuser',
                'value' => function($model){
                    if ($model->isSuperUser())
                    {
                        
                        return Yii::t('app', 'TAK')." (".$model->getSuperUser().")";
                    }else{
                            if ($model->isSuperUser(2))
                            {
                        
                                return Yii::t('app', 'TAK - User+')." (".$model->getSuperUser(2).")";
                            }else{
                                return Yii::t('app', 'NIE');
                        }
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>[1=>Yii::t('app', 'TAK'), 2=>Yii::t('app', 'NIE')],
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{history} {view} {update} {delete}',
                'buttons' => [
                    'history' => function ($url, $model, $key) {
                        return Html::a(Html::icon('list'), $url);
                    },
                ],
                'visibleButtons' => [
                    'update'=>$user->can('usersUsersEdit'),
                    'delete'=>$user->can('usersUsersDelete'),
                    'view'=>$user->can('usersUsersView'),
                    'history'=>$user->can('usersUsersHistory'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>
