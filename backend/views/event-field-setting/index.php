<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Dodatkowe pola w wydarzeniu');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="event-field-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
        'attribute' =>'type',
        'value'=>function($model){
            return \common\models\EventFieldSetting::getTypeList()[$model->type];
        }],
        [
            'attribute' => 'column_in_list',
            'value' => function($model)
            {
                if ($model->column_in_list)
                {
                    return Yii::t('app', 'TAK');
                }else{
                    return Yii::t('app', 'NIE');
                }
            }
        ],
         [
            'attribute' => 'visible_on_packlist',
            'value' => function($model)
            {
                if ($model->visible_on_packlist)
                {
                    return Yii::t('app', 'TAK');
                }else{
                    return Yii::t('app', 'NIE');
                }
            }
        ],       
        
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-event-field-setting']],
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
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]) ,
        ],
    ]); ?>

</div>
