<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var $dataProvider array
 * @var $filterModel  dektrium\rbac\models\Search
 * @var $this         yii\web\View
 */


use kartik\select2\Select2;
use yii\grid\ActionColumn;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Html;

$this->title = Yii::t('app', 'Grupy użytkowników');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>

<p>
<?php
    if ($user->can('settingsAccessControlAdd')) {
        echo Html::a(Html::icon('plus') . ' '.Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']); 
        echo " ".Html::a(Html::icon('list') . ' '.Yii::t('app', 'Ustawienia uprawnień'), ['/permission/default/manage-roles2'], ['class'=>'btn btn-success']);
    }

?>
</p>

<?php Pjax::begin() ?>
<div class="panel_mid_blocks">
    <div class="panel_block">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'kv-grid-table table table-condensed kv-table-wrap'
    ],
    'filterModel'  => $filterModel,
    'layout'       => "{items}\n{pager}",
    'columns'      => [
        [

            'attribute' => 'name',
            'header'    => Yii::t('app', 'Nazwa'),
            'filter' => Select2::widget([
                'model'     => $filterModel,
                'attribute' => 'name',
                'data'      => $filterModel->getNameList(),
                'options'   => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]),
        ],
        [

            'attribute' => 'superuser',
            'header'    => Yii::t('app', 'SuperUser'),
            'filter' => Select2::widget([
                'model'     => $filterModel,
                'attribute' => 'superuser',
                'data'      => [0=>Yii::t('app', 'NIE'), 1=>Yii::t('app', 'TAK')],
                'options'   => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]),
            'value' => function($model){
                if ($model['superuser'])
                    return Yii::t('app', 'TAK');
                else
                    return Yii::t('app', 'NIE');
            }
        ],
        [
            'class'      => ActionColumn::className(),
            'template'   => '{update} {delete}',
            'urlCreator' => function ($action, $model) {
                return Url::to([ $action, 'name' => $model['name']]);
            },
            'options' => [
                'style' => 'width: 5%'
            ],
            'visibleButtons' => [
                'update' => $user->can('settingsAccessControlManageEdit'),
                'delete' => $user->can('settingsAccessControlManageDelete')
            ]
        ]
    ],
]) ?>
    </div>
</div>
<?php Pjax::end() ?>
