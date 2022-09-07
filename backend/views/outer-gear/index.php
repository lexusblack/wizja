<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OuterGearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sprzęt zewnętrzny');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-index">

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['outer-gear/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            'quantity',
            [
                'label' => Yii::t('app', 'Sztuk w magazynie'),
                'value' => function ($model) {
                    return $model->numberOfAvailable();
                }
            ],
            'brightness',
            'category.name',
            'price',
            'selling_price',
            [
                'label'=>Yii::t('app', 'Zysk'),
                'value' => function ($model, $key, $index, $column)
                {
                    if (isset($model->price) && isset($model->selling_price))
                    {
                        return $model->selling_price - $model->price;
                    }
                    return null;
                },
                'filter'=>false,
            ],
            ['class' => 'yii\grid\ActionColumn']
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