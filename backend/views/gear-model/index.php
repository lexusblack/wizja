<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Baza sprzętu');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-model-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (($user->can('gearBaseCreate'))&&(Yii::$app->params['companyID']=='newsystem')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="search-form" style="display:none">
        <?=  $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php 
    if ($t=="inner") {
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'label' => Yii::t('app', 'Import'),
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a('<i class="fa fa-download"></i>', ['gear-model/import', 'id' => $model['id']], ['class'=>'btn btn-info btn-circle', 'target'=>'_blank']) ;                  
            },
            'visible' => $user->can('gearBaseImport')
        ],
                ['class'=>\common\components\grid\PhotoColumn::className()],
                [
                'attribute' => 'company_id',
                'label' => Yii::t('app', 'Producent'),
                'value' => function($model){
                    if ($model->company)
                    {return $model->company->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCompany::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Producent'), 'id' => 'grid-gear-model-search-comapany_id']
            ],
        [
            'label' => Yii::t('app', 'Nazwa'),
            'format' => 'html',
            'attribute'=>'name',
            'value' => function ($model) {
                return Html::a($model['name'], ['gear-model/view', 'id' => $model['id']]) ;
            }
        ],
        [
                'attribute' => 'category_id',
                'label' => Yii::t('app', 'Kategoria'),
                'value' => function($model){
                    if ($model->category)
                    {return $model->category->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\GearModelCategory::getNoEmptyList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Kategoria'), 'id' => 'grid-gear-model-search-category_id']
            ],
        'weight',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'visibleButtons' => [
                    'update'=>$user->can('gearBaseEdit')
                ]
            ],
    ]; 
    }else{
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'label' => Yii::t('app', 'Import'),
            'format' => 'html',
            'value' => function ($model) {
                return Html::a('<i class="fa fa-download"></i>', ['gear-model/importouter', 'id' => $model['id']], ['class'=>'btn btn-info btn-circle']) ;                  
            },
            'visible' => $user->can('gearBaseImport')
        ],
                ['class'=>\common\components\grid\PhotoColumn::className()],
                [
                'attribute' => 'company_id',
                'label' => Yii::t('app', 'Producent'),
                'value' => function($model){
                    if ($model->company)
                    {return $model->company->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCompany::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Producent'), 'id' => 'grid-gear-model-search-comapany_id']
            ],
        [
            'label' => Yii::t('app', 'Nazwa'),
            'format' => 'html',
            'attribute'=>'name',
            'value' => function ($model) {
                return Html::a($model['name'], ['gear-model/view', 'id' => $model['id']]) ;
            }
        ],
        [
                'attribute' => 'category_id',
                'label' => Yii::t('app', 'Kategoria'),
                'value' => function($model){
                    if ($model->category)
                    {return $model->category->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCategory::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Kategoria'), 'id' => 'grid-gear-model-search-category_id']
            ],
        'weight',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update'=>$user->can('gearBaseEdit'),
                    'delete'=>$user->can('gearBaseDelete'),
                    'view'=>$user->can('gearBaseView'),
                ]
            ],
    ];         
    }
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-model']],
        'export' => false,
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">'.Yii::t('app', 'Eksportuj dane').'</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]) ,
        ],
    ]); ?>

</div>
