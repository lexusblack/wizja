<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Języki');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="language-index">

    <p>
        <?php
        if ($user->can('settingsLanguageLanguagesAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel panel-default">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'code',
            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'update' => $user->can('settingsLanguageLanguagesEdit'),
                        'delete' => $user->can('settingsLanguageLanguagesDelete'),
                        'view' => $user->can('settingsLanguageLanguagesView')
                ]
            ],

        ],
    ]); ?>
    </div>
</div>