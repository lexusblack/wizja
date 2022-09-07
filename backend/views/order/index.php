<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Zamówienia');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'label'=>Yii::t('app', 'Nr')],
        [
                'attribute' => 'company_id',
                'label' => Yii::t('app', 'Firma'),
                'value' => function($model){
                    if ($model->company)
                    {return $model->company->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-order-search-company_id']
            ],
        [
                'attribute' => 'contact_id',
                'label' => Yii::t('app', 'Osoba kontaktowa'),
                'value'=>'contact.displayLabel',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\Contact::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-order-search-contact_id']
            ],
        'confirm',
        'create_at',
        [
                'attribute' => 'user_id',
                'label' => Yii::t('app', 'Utworzył'),
                'value' => function($model){
                    if ($model->user)
                    {return $model->user->username;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' =>  \common\models\User::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz...'), 'id' => 'grid-order-search-user_id']
            ],
        [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions'=>['style'=>'width: 80px;'],

        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-order']],
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
                    'label' => Yii::t('app', 'Pełny'),
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
