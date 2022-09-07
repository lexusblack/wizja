<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */

use common\components\grid\GridView;
use yii\bootstrap\Html;
use common\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;

Modal::begin([
    'header' => Yii::t('app', 'Flota'),
    'id' => 'vehicle_modal',
        'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();


//echo $this->render('_tools');
$this->title = Yii::t('app', 'Zarządzaj flotą').' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['event/view', 'id'=>$model->id, '#' => 'tab-vehicle']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Zarządzaj flotą');
?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
            <?php echo Html::a(Html::icon('arrow-left').' Zapisz i wróć', ['event/view', 'id'=>$model->id, '#' => 'tab-vehicle'], ['class'=>'btn btn-primary']); ?>
            <div class="alert alert-info">
                    <b><u><?= Yii::t('app', 'Zapotrzebowanie z ofert:') ?></u></b><br/>
                        <?php 
                        foreach (\common\models\EventOfferVehicle::find()->where(['event_id'=>$model->id])->all() as $vehicle){
                            echo $vehicle->schedule." ".$vehicle->quantity."x ".$vehicle->vehicle->name." [".$vehicle->vehicle->capacity."kg] [".$vehicle->vehicle->volume."m3]<br/>";
                            } ?>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions'=> function ($item, $key, $index, $grid) use ($model)
                {
                    return [
                        'class'=> $item->isAvailable($model) ? '' : 'danger',
                    ];
                },
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($item, $key, $index, $column) use ($assignedItems, $model) {
                            return [
                                'checked' => in_array($item->id, $assignedItems),
                            ];
                        }
                    ],

                    ['class' => 'yii\grid\SerialColumn'],

                    ['class'=>\common\components\grid\PhotoColumn::className()],
                    'name',
                    [
                        'label' => Yii::t('app', 'Zajęte'),
                        'value' => function ($item, $key, $index, $grid) use ($model)
                        {
                            /* @var $item \common\models\Vehicle */
                            $data = $item->getUnavailableRanges($model, true);
                            return implode(' ', $data);
                        },
                        'format' => 'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Serwis'),
                        'value' => function ($item)
                        {
                            if (!$item->status)
                                return Yii::t('app', 'Uwaga! Samochód w serwisie');
                        }
                    ]
                ],
            ]); ?>
        </div>

    </div>






<?php
$assignUrl = Url::to(['vehicle/assign-vehicle', 'id'=>$model->id]);
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
        if (!add)
            $.post("'.$assignUrl.'", data, function(response){toastr.error("'.Yii::t('app', 'Pojazd usunięty z eventu').'");});
        else{
            $.post("'.$assignUrl.'", data, function(response){toastr.success("'.Yii::t('app', 'Pojazd dodany do eventu').'");}); 
            openVehicleModal('.$model->id.', id);  

        }
    });
}

function openVehicleModal(event_id, vehicle_id){
    var modal = $("#vehicle_modal");
    modal.find(".modalContent").load("'.Url::to(["planboard/vehicle-form"]).'?event_id="+event_id+"&vehicle_id="+vehicle_id);
    modal.modal("show");
}

');

