<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Modele sprzętu');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-index">

    <p>
        <?php if ($user->can('gearCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
        <?php if ($user->can('gearCreate')) {
            echo Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Import'), ['import2'], ['class' => 'btn btn-success']);
        } 
        echo " ".Html::a(Yii::t('app', 'Lista usuniętych'), ['deleted'], ['class' => 'btn btn-danger']);
        ?>
    </p>
    <div class="panel_mid_blocks">
    <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Sztuk na stanie'),
                'value'=>function($gear, $key, $index, $column)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items)
                    {
                        return $gear->quantity;
                    }
                    else
                    {
                        return $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                }
            ],
            [
                'attribute'=>'visible_in_warehouse',
                'label'=>'Widoczny w magazynie',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => [1=>"TAK", 0=>"NIE"],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Widoczność'), 'id' => 'grid-gear-model-search-visible_id'],
                'value'=>function($model){
                    if ($model->visible_in_warehouse==1)
                    {
                        return "TAK";
                    }else{
                        return "NIE";
                    }
                }
            ],
                        [
                'attribute'=>'visible_in_offer',
                'label'=>'Widoczny w ofercie',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => [1=>"TAK", 0=>"NIE"],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Widoczność'), 'id' => 'grid-gear-model-search-visibleoffer_id'],
                'value'=>function($model){
                    if ($model->visible_in_offer==1)
                    {
                        return "TAK";
                    }else{
                        return "NIE";
                    }
                }
            ],
                        [
                'attribute'=>'conflict_outcome',
                'label'=>'Wydanie mimo konfliktu',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => [1=>"TAK", 0=>"NIE"],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz'), 'id' => 'grid-gear-model-search-visibleoffer_id'],
                'value'=>function($model){
                    if ($model->conflict_outcome==1)
                    {
                        return "TAK";
                    }else{
                        return "NIE";
                    }
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
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('gearEdit'),
                    'delete'=>$user->can('gearDelete'),
                    'view'=>$user->can('gearView'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>

<?php

$this->registerJs('

    $("object").each(function(){
        var data = $(this).attr("data");
        var name = $(this).parent().data("name");
        
        $(this).wrap("<a href=\'" + data + "\' download=\'" + name + ".bmp\'></a>");
    });

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

');