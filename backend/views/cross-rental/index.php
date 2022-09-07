<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\CrossRentalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Cross Rental Network');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-rental-index">
<div class="clearfix">
<?php
echo \common\widgets\CategoryMenuWidget2::widget([
        'btnOptions' => [
            'class' => 'auto-save category-menu-link',
        ]
]);
?>
</div>
<div class="row">
    <div class="col-md-12">
     <div class="search-form">
                <?php echo Html::beginForm(['index'], 'get', ['class'=>'form-inline']); ?>

            <div class="form-group">
                <?php echo Html::textInput('CrossRentalSearch[name]', $searchModel->name, ['placeholder'=>Yii::t('app', 'Nazwa sprzętu'), 'class'=>'form-control', 'autocomplete'=>"off"]); ?>
                <?php echo Html::textInput('CrossRentalSearch[owner_city]', $searchModel->owner_city, ['placeholder'=>Yii::t('app', 'Miasto'), 'class'=>'form-control', 'autocomplete'=>"off"]); ?>
            </div>
        
            <button type="submit" class="btn btn-primary btn-sm"><?= Yii::t('app', 'Szukaj') ?></button>
            <?php echo Html::endForm(); ?>
        </div>
    </div>
    
</div>
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
                'attribute' => 'name',
                'label' => Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value' => function($model){
                    if ($model->gearModel)
                    {
                        return Html::a($model->gearModel->name, ['view', 'id'=>$model->id]);
                    }
                    else
                    {return NULL;}
                },
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
            'label' => '',
            'format'=>'raw',
            'value'=>function($model)
            {
                if ($model->owner!=Yii::$app->params['companyID'])
                    return Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$model->id], ['class'=>['btn btn-sm btn-primary send-crn-request']]);
                else
                    return "";
            }
        ]
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterSelector'=>'.grid-filters',
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => false,
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


<?php $this->registerJs('
    $(".send-crn-request").click(function(e)
    {
        e.preventDefault();
        $.get($(this).attr("href"), function(data){
                openMessageDialog(data.id, 2);
            }); 
    })
    ');