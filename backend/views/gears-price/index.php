<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Stawki');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gears-price-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Edytuj ceny'), ['/gear/prices'], ['class' => 'btn btn-success']) ?>
    </p>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
            'attribute'=>'type',
            'value'=>function ($model){
                return \common\models\GearsPrice::getTypeList()[$model->type];
            }
        ],
        [
            'attribute'=>'gear_id',
            'value'=>function ($model){
                if ($model->gear_id)
                    return $model->gear->name;
                else
                    return "-";
            }
        ],
        [
            'attribute'=>'gear_category_id',
            'value'=>function ($model){
                if ($model->gear_category_id)
                    return $model->gearCategory->name;
                else
                    return "-";
            }
        ],
        'currency',
        'vat',
        [
            'label' => Yii::t('app', 'Cena za kolejne dni'),
            'format'=>'raw',
            'value'=> function($model)
            {
                $content = "";
                foreach ($model->gearsPricePercents as $p)
                {
                    $content .= Yii::t('app', "Od ").$p->day.Yii::t('app', " dzień")." - ".$p->value."%<br/>";
                }
                return $content;
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
        'pjax' => false,
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
