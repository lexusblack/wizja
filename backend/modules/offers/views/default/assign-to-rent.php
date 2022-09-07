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
        <?php echo Html::a(Html::icon('arrow-left').' '.$rent->name, ['/rent/view', 'id'=>$rent->id], ['class'=>'btn btn-warning']); ?>
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
                'checkboxOptions' => function ($model, $key, $index, $column) use ($rent) {
                    return ['checked' => ($model->rent_id == $rent->id), 'class'=>'checkbox-model'];
                }
            ],
            'id',
            [
                'attribute'=>'customer_id',
                'filter'=> \common\models\Customer::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    $list = \common\models\Customer::getList();
                    return $list[$model->customer_id];
                },
            ],
            'name',
            [
                'attribute'=>'location_id',
                'filter'=> \common\models\Location::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    return $model->getLocationLabel();
                },
            ],
            'term_from',
            'term_to',
            [
                'attribute'=>'manager_id',
                'filter'=> \common\models\User::getList(),
                'value' => function($model, $key, $index, $column)
                {
                    $list = \common\models\User::getList();
                    return $list[$model->manager_id];
                },
            ],
            'offer_date',
            'comment:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
        </div>
    </div>
    </div>
</div>

<?php
$eventGearUrl = Url::to(['/offer/default/offer-rent', 'rent_id'=>$rent->id]);
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

    if(add == false){
        if (confirm("'.Yii::t('app', "Po usunięciu wszystkie przypisane do oferty egzemplarzy będą też usunięty").'")) {
            eventOffer(id, add);
        } else {
            $(this).prop("checked",true);
        }
    } else {
        eventOffer(id, add);
    }
    
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
