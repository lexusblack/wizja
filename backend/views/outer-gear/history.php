<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Historia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">
    <p>
        <?= Html::a(Html::icon('arrow-left') . ' '.Yii::t('app', 'Magazyn zewnętrzny'), Url::previous('outer-warehouse'), ['class' => 'btn btn-success']) ?>
    </p>
    <h3><?= Yii::t('app', 'Wydarzenia') ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'value'=>'location.displayLabel',
                'filter' => \common\models\Location::getList(),
                'attribute' => 'location_id',
            ],
            [
                'value'=>'customer.displayLabel',
                'filter' => \common\models\Customer::getList(),
                'attribute' => 'customer_id',
            ],
            [
                'value'=>'contact.displayLabel',
                'filter' => \common\models\Contact::getList(),
                'attribute' => 'contact_id',
            ],
            [
                'label'=>Yii::t('app', 'Od - do'),
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
            ],
        ],
    ]); ?>
</div>