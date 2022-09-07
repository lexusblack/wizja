<?php
use kartik\form\ActiveForm;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
\common\assets\AreYouSureAsset::register($this);
use yii\helpers\Url;

/* @var $model \common\models\Offer */

$this->title = Yii::t('app', 'Dodaj pojazdy');
$this->params['breadcrumbs'][] = ['label' => 'Oferty', 'url' => ['/offer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/offer/default/view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="offer-role-add-form">
<div class="ibox float-e-margins">
    <?php echo Html::a(Html::icon('arrow-left')." ".$model->name, ['/offer/default/view', 'id'=>$model->id], ['class'=>'btn btn-primary btn-sm']); ?>
    <div class="alert alert-info">
    <b><u><?= Yii::t('app', 'Zapotrzebowanie transport osób:') ?></u></b><br/>
                        <?php 
                        $labels = [1=>Yii::t('app', 'Pakowanie'), 2=>Yii::t('app', 'Montaż'), 3=>Yii::t('app', 'Event'), 4=>Yii::t('app', 'Demontaż')];
                        for ($i=1; $i<5; $i++){
                            $count = $model->getWorkersCount($i);
                            echo $labels[$i].": ".$count.Yii::t('app', 'os.')."<br/>";

                             }?>
                             <b><u><?= Yii::t('app', 'Zapotrzebowanie transport sprzętu:') ?></u></b><br/>
                        <?php  echo Yii::t('app', 'Objętość: ').round($model->getTotalVolumeAndWeight()['volume'], 2); ?> <?= Yii::t('app', 'm') ?><sup>3</sup> 
                        <?= Yii::t('app', 'Waga netto: ').$model->getTotalVolumeAndWeight()['weight']; ?> <?= Yii::t('app', 'kg') ?>
    </div>


</div>
<?php foreach ($model->offerSchedules as $schedule){ $i = $schedule->id?> 
<div class="ibox float-e-margins">
    <div class="ibox-title newsystem-bg">
        <h5><?php echo $schedule->name." ".$schedule->getPeriodTime()."h"; ?></h5>
        <?php if ($schedule->position>0){ echo Html::a("<i class='fa fa-copy'></i> ".Yii::t('app', 'Kopiuj z poprzedniego'), ['/offer/default/copy-vehicles', 'time_type'=>$i, 'offer_id'=>$model->id], ['class'=>'btn btn-xs pull-right white-button']); } 
            echo Html::a("<i class='fa fa-truck'></i> ".Yii::t('app', 'Licz odległość z Google'), ['/offer/default/count-km', 'offer_id'=>$model->id], ['class'=>'btn btn-xs pull-right btn-primary count-km']);
        ?>
    </div>
    <div class="ibox-content">
    <div class="role-list">
    <div class="row">
    <div class="col-xs-4"><?=Yii::t('app', 'Nazwa')?></div><div class="col-xs-1"><?=Yii::t('app', 'Cena')?></div><div class="col-xs-1"><?=Yii::t('app', 'Liczba')?></div><div class="col-xs-1"><?=Yii::t('app', 'Przelicznik')?></div><div class="col-xs-1"><?=Yii::t('app', 'jedn.')?></div><div class="col-xs-2"><?=Yii::t('app', 'Stawka')?></div><div class="col-xs-2"><?=Yii::t('app', 'Razem netto')?></div></div>
    <?php $j=1; foreach ($model->getOVehicle($i) as $vehicle) { ?>

    <div class="row">
            <?php $form = ActiveForm::begin([
        'id' => 'offer-vehicle-'.$vehicle->id,
        'options'=>[
            'class'=>'offervehicleform'
        ]
            ]); ?>
            <?php echo  $form->field($vehicle, 'id')->hiddenInput()->label(false); ?>
    <div class="col-xs-4">
            <?php echo $form->field($vehicle, 'vehicle_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\VehicleModel::getList($vehicle->vehicle_id),
                
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id'=>'offervehicle'.$vehicle->id,
                    'class'=>'oferrole'
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])
                ->label(false);
            ?>
</div><div class="col-xs-1 no-padding">
            <?php echo  $form->field($vehicle, 'price')->textInput(['maxlength' => true, 'style'=>'padding-left:0px; padding-right:0px;'])->label(false); ?>
            </div><div class="col-xs-1">
            <?php echo  $form->field($vehicle, 'quantity')->textInput(['maxlength' => true])->label(false); ?>
            </div><div class="col-xs-1">
            <?php echo  $form->field($vehicle, 'distance')->textInput(['maxlength' => true, 'style'=>'padding-left:0px; padding-right:0px;'])->label(false); ?>
            
            </div><div class="col-xs-1">
            <?php echo  $form->field($vehicle, 'unit')->textInput(['maxlength' => true, 'style'=>'padding-left:0px; padding-right:0px;'])->label(false); ?>
            
            </div><div class="col-xs-2 no-padding">
            <?php echo  $form->field($vehicle, 'vehicle_price_id')->dropDownList(\common\models\VehiclePrice::getList($vehicle->vehicle_id, $model->priceGroup->currency), ['data-period'=>$schedule->getPeriodTime()])->label(false); ?>
            </div><div class="col-xs-2" style="padding-top:6px; padding-left:10px;">
            <div class="total-netto pull-left"><?=$vehicle->price*$vehicle->quantity*$vehicle->distance?></div>
            <span class="pull-right"><?php echo Html::a(Html::icon('trash'), ['/offer/default/delete-vehicle', 'id' => $vehicle->id], ['class'=>'btn-xs btn btn-danger role-delete']); ?></span>
            </div>
            <?php ActiveForm::end(); ?>
             </div>
            
           
    <?php $j++; } ?>
    </div>
    <?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/offer/default/add-vehicle', 'time_type'=>$i, 'offer_id'=>$model->id], ['class'=>'btn btn-primary btn-sm add-vehicle']); ?>    
    </div>
</div>
<?php } ?>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
$saveRoleUrl = Url::to(['/offer/default/save-vehicle']);
$this->registerJs('
       $(".count-km").click(function(e){
        e.preventDefault();
         var _this = $(this);
        $.ajax({url: $(this).attr("href"), success: function(result){
            _this.parent().parent().find("form").each(function(){
                form = $(this);
                form.find("#offervehicle-distance").val(result.distance);
                price = form.find("#offervehicle-price").val();
                duration = form.find("#offervehicle-distance").val();
                quantity = form.find("#offervehicle-quantity").val();
                total = price*duration*quantity;
                form.parent().find(".total-netto").empty().append(total.toFixed(2));
                $.ajax({
                  type: "POST",
                  url: "'.$saveRoleUrl.'",
                  data: form.serialize()
                });
            });
            }});
    }); 

    $(".offervehicleform input").change(function(){
        form = $(this).closest("form");
        price = form.find("#offervehicle-price").val();
        duration = form.find("#offervehicle-distance").val();
        quantity = form.find("#offervehicle-quantity").val();
        total = price*duration*quantity;
        $(this).parent().parent().parent().find(".total-netto").empty().append(total.toFixed(2));
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrl.'",
          data: form.serialize()
        });
    });

$(".offervehicleform select").on("focusin", function(){
    console.log("Saving value " + $(this).val());
    $(this).data("val", $(this).val());
})

    $(".offervehicleform select").change(function(){
        if ($(this).hasClass("oferrole"))
        {
                form = $(this).closest("form");              
                $.ajax({
                  type: "POST",
                  url: "'.$saveRoleUrl.'?new_vehicle=1",
                  data: form.serialize(), 
                  success: function(result){
                    p = form.parent();
                    p.empty();
                    p.append(result);
                  }
                });       
        }else{
            form = $(this).closest("form");
                $.ajax({
                  type: "POST",
                  url: "'.$saveRoleUrl.'?new_group=1",
                  data: form.serialize(), 
                  success: function(result){
                    form.find("#offervehicle-price").val(result.price);
                    form.find("#offervehicle-vehicle_price_id").val(result.vehicle_price_id);
                    form.find("#offervehicle-unit").val(result.unit);
                    price = form.find("#offervehicle-price").val();
                    duration = form.find("#offervehicle-distance").val();
                    quantity = form.find("#offervehicle-quantity").val();
                    total = price*duration*quantity;
                    form.parent().find(".total-netto").empty().append(total.toFixed(2));
                  }
                });
        }

    });


    $(".add-vehicle").click(function(e){
        e.preventDefault();
         var _this = $(this);
        $.ajax({url: $(this).attr("href"), success: function(result){
            _this.parent().parent().find(".role-list").append("<div class=\'row\'>"+result+"</div>");
                $(".role-delete").click(function(e){
                    e.preventDefault();
                    var _this = $(this);
                    $.ajax({url: $(this).attr("href"), success: function(result){
                                _this.parent().parent().parent().remove();
                        }});
                });
    }});
    });

    $(".role-delete").click(function(e){
        e.preventDefault();
         var _this = $(this);
        role = _this.parent().parent().parent().find("select").val();
        if (role)
        {
        $.ajax({url: $(this).attr("href")+"&role_id="+role, success: function(result){
            _this.parent().parent().parent().remove(); 
            }});           
        }else{
            _this.parent().parent().parent().remove();
        }


    
    });
');