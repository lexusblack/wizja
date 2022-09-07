<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearPurchaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Zakupy');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-purchase-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
                      'format' => 'html',
              'attribute' => 'gear_id',
                'value' => function($model){
                    return Html::a($model->gear->name, ['gear/view', 'id' => $model->gear_id]);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->andWhere(['type'=>3])->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz sprzęt'), 'id' => 'grid-gear-purchase-search-gear_id']
            ],
                [
                    'attribute'=> 'datetime',
                    'value'=> function($model)
                    {
                        return substr($model->datetime, 0, 10);
                    }
                ],
        [
                     'format' => 'html',
               'attribute' => 'customer_id',
                'value' => function($model){
                    return Html::a($model->customer->name, ['customer/view', 'id' => $model->customer_id]);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz kontrahenta'), 'id' => 'grid-gear-purchase-search-customer_id']
            ],
                'price',
                [
                    'attribute' => 'quantity',
                    'label' => Yii::t('app', 'Liczba sztuk'),
                    'value'=>function($model)
                    {
                           return $model->quantity;

                    },                    
                ],
                'total_price',
                [
                    'attribute' => 'expense_id',
                    'label' => Yii::t('app', 'FV'),
                    'value'=>function($model)
                    {
                           if (isset($model->expense))
                            return $model->expense->number;
                        else
                            return "-";

                    },                    
                ],
        [
                'attribute' => 'user_id',
                'label'=>Yii::t('app', 'Dodał'),
                'value' => function($model){
                    if ($model->user)
                    {return $model->user->displayLabel;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->asArray()->all(), 'id', 'last_name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz użytkownika'), 'id' => 'grid-gear-purchase-search-user_id']
            ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
