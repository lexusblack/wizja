<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Oferty');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a(Html::icon('arrow-left').' '.$event->name, ['/event/view', 'id'=>$event->id], ['class'=>'btn btn-warning']); ?>
    </p>
    <div class="grid">
        <div class="panel_mid_blocks">
            <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>false,
                'checkboxOptions' => function ($model, $key, $index, $column) use ($event) {
                    return ['checked' => ($model->event_id == $event->id), 'class'=>'checkbox-model'];
                }
            ],
            'id',
        'name',
        [
                'attribute' => 'customer_id',
                'label' => 'Customer',
                'value' => function($model){
                    if ($model->customer)
                    {return $model->customer->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Customer', 'id' => 'grid-agency-offer-search-customer_id']
            ],
        [
                'attribute' => 'manager_id',
                'label' => 'Manager',
                'value' => function($model){
                    if ($model->manager)
                    {return $model->manager->username;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->asArray()->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'User', 'id' => 'grid-agency-offer-search-manager_id']
            ],
        [
                'attribute' => 'event_id',
                'label' => 'Event',
                'value' => function($model){
                    if ($model->event)
                    {return $model->event->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Event', 'id' => 'grid-agency-offer-search-event_id']
            ],
        ],
    ]); ?>
        </div>
    </div>
    </div>
</div>

<?php
$eventGearUrl = Url::to(['/agency-offer/offer-event', 'event_id'=>$event->id]);
$this->registerJs('

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

$(".grid :checkbox").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventOffer(id, add);
    
});

function eventOffer(id, add)
{
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$eventGearUrl.'", data, function(response){
        
    });
}');
