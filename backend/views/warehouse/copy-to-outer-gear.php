<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use demogorgorn\ajax\AjaxSubmitButton;
use yii\widgets\Pjax;
use kartik\dialog\Dialog;
\kartik\growl\GrowlAsset::register($this);
\kartik\base\AnimateAsset::register($this);
?>

<?php
echo $this->render('_categoryMenu');
echo $this->render('_tools', ['warehouse'=>$warehouse]);

?>
<div class="warehouse-container">

    <?php Pjax::begin([
        'id'=>'warehouse-pjax-container',
    ]); ?>
    <div class="gear gears">
        <h3><?php echo $title; ?></h3>
        <?php
        $gearColumns = [
            [
                'content'=>function($model, $key, $index, $grid) use ($warehouse)
                {
                    $icon = 'plus-sign';
                    return Html::a(Html::icon($icon), Url::to(['outer-gear/create','import_gear_id'=>$model->id]));

                }
            ],
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['gear/update', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'attribute'=>'quantity',
                'value'=>function($gear, $key, $index, $column)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items==true)
                    {
                        return $gear->quantity;
                    }
                    else
                    {
                        return $gear->getGearItems()->count();
                    }
                }
            ],
            [
                'attribute'=>'available',
                'value'=>function($gear, $key, $index, $column) use ($warehouse)
                {
                    return $warehouse->getGearAvailableCount($gear);
                }
            ],
            'brightness:decimal',
            'power_consumption:decimal',
            [
                'attribute'=>'weight',
                'value' => function ($model, $key, $index, $column)
                {
                    if ($model->weight)
                    {
                        return Yii::$app->formatter->asDecimal($model->weight).' '.Yii::t('app', 'kg');
                    }
                    return null;
                }
            ],
            [
                'attribute'=>'width',
                'value' => function ($model, $key, $index, $column)
                {
                    if ($model->width)
                    {
                        return Yii::$app->formatter->asDecimal($model->width).' '.Yii::t('app', 'cm');
                    }
                    return null;
                }
            ],
            [
                'attribute'=>'height',
                'value' => function ($model, $key, $index, $column)
                {
                    if ($model->height)
                    {
                        return Yii::$app->formatter->asDecimal($model->height).' '.Yii::t('app', 'cm');
                    }
                    return null;
                }
            ],
            [
                'attribute'=>'depth',
                'value' => function ($model, $key, $index, $column)
                {
                    if ($model->depth)
                    {
                        return Yii::$app->formatter->asDecimal($model->depth).' '.Yii::t('app', 'cm');
                    }
                    return null;
                }
            ],
            [
                'attribute'=>'volume',
                'value' => function ($model, $key, $index, $column)
                {
                    if ($model->depth)
                    {
                        return Yii::$app->formatter->asDecimal($model->getCalculatedVolume()).' '.Yii::t('app', 'cm').'<sup>3</sup>';
                    }
                    return null;
                },
                'format'=>'html',
            ],
            'price:currency',
            ];

        echo GridView::widget([
            'dataProvider' => $warehouse->getGearDataProvider(),
            'filterModel' => null,
            'columns' => $gearColumns,
        ]); ?>
    </div>

</div>

<?php Pjax::end(); ?>
