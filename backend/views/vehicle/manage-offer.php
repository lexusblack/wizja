<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */

use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Wybierz pojazdy');
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/offer/default/view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
        <div class="ibox float-e-margins">
            <?php echo Html::a(Html::icon('arrow-left').' '.$model->name, ['/offer/default/view', 'id'=>$model->id], ['class'=>'btn btn-warning']); ?>
        </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
    <div class="ibox float-e-margins">
    <div class="ibox-title newsystem-bg">
                            <h5><?php echo Yii::t('app', 'Flota'); ?></h5>
    </div>
    <div class="ibox-content">
    <div class="row">
                        <div class="col-md-6"><b><u><?= Yii::t('app', 'Zapotrzebowanie transport osób:') ?></u></b><br/>
                        <?php 
                        $labels = [1=>Yii::t('app', 'Pakowanie'), 2=>Yii::t('app', 'Montaż'), 3=>Yii::t('app', 'Event'), 4=>Yii::t('app', 'Demontaż')];
                        for ($i=1; $i<5; $i++){
                            $count = $model->getWorkersCount($i);
                            echo $labels[$i].": ".$count.Yii::t('app', 'os.')."<br/>";

                             }?>
                        </div>
                        <div class="col-md-6"><b><u><?= Yii::t('app', 'Zapotrzebowanie transport sprzętu:') ?></u></b><br/>
                        <?php $size = $model->getGearSize(); echo Yii::t('app', 'Objętość: ').round($size['volume'], 2); ?> <?= Yii::t('app', 'm') ?><sup>3</sup> 
                        <?= Yii::t('app', 'Waga: ').$size['weight'] ?> <?= Yii::t('app', 'kg') ?>

                        </div>
                    </div>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,

                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) use ($assignedItems) {
                            return ['checked' => in_array($model->id, $assignedItems)];
                        }
                    ],

                    ['class' => 'yii\grid\SerialColumn'],

                    ['class'=>\common\components\grid\PhotoColumn::className()],
                    'name',
                    'name_in_offer',
                    'capacity',
                    'volume',
                    'price_km',
                    'price_city'

//                    [
//                        'class' => 'yii\grid\ActionColumn',
//                        'urlCreator' =>  function($action, $model, $key, $index)
//                        {
//                            $params = is_array($key) ? $key : ['id' => (string) $key];
//                            $params[0] = 'user/' . $action;
//
//                            return Url::toRoute($params);
//                        }
//                    ],
                ],
            ]); ?>
        </div>
    </div>
        </div>
    </div>






<?php
$assignUrl = Url::to(['vehicle/assign-offer-vehicle', 'id'=>$model->id]);
$this->registerJs('
$(":checkbox").not(".select-on-check-all").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var id = $(this).val();
    eventGear(id, add);
});
$(":checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var elements = $(this).closest("table").find("tbody :checkbox");
    elements.each(function(index,el)
    {
        var id = $(el).val();
        var add = $(el).prop("checked");
        eventGear(id, add);
    });
    
});

function eventGear(id, add)
{
    var data = {
        itemId : id,
        add : add ? 1 : 0
    }
    $.post("'.$assignUrl.'", data, function(response){
        console.log(response);
    });
}

');



