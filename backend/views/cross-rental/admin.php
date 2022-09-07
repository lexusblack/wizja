<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\CrossRentalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Cross Rental Network');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-rental-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
            [
                'label' => Yii::t('app', 'Zdjęcie'),
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if (!$model->gearModel || $model->gearModel->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->gearModel->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
        [
                'attribute' => 'gear_model_id',
                'label' => Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value' => function($model){
                    if ($model->gearModel)
                    {
                        return $model->gearModel->name;
                    }
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearModel::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Nazwa'), 'id' => 'grid-cross-rental-search-gear_model_id']
        ],
        [
            'attribute' => 'owner_name',
                'format'=>'html',
                'value' => function($model){
                    if ($model->gearModel)
                    {
                        $return = $model->owner_name;
                        $return .= "<br/>".$model->owner_address." ".$model->owner_city;
                        if ($model->owner_phone)
                            $return .= "<br/>".Yii::t('app', "tel").". ".$model->owner_phone;
                        $return .= "<br/>".$model->owner_mail;
                        return $return;
                    }
                    else
                    {return NULL;}
                },
        ],
        'owner_city',
        'owner_country',
        'quantity',
        [
                'class' => 'yii\grid\ActionColumn',

        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-cross-rental']],
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
