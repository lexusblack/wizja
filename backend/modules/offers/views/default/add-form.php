<?php
use kartik\form\ActiveForm;
use kartik\helpers\Html;
\common\assets\AreYouSureAsset::register($this);
use yii\helpers\Url;
$j++;
?>

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
            <?php echo  $form->field($vehicle, 'vehicle_price_id')->dropDownList(\common\models\VehiclePrice::getList($vehicle->vehicle_id, $currency))->label(false); ?>
            </div><div class="col-xs-2" style="padding-top:6px; padding-left:10px;">
            <div class="total-netto pull-left"><?=$vehicle->price*$vehicle->quantity*$vehicle->distance?></div>
            <span class="pull-right"><?php echo Html::a(Html::icon('trash'), ['/offer/default/delete-vehicle', 'id' => $vehicle->id], ['class'=>'btn-xs btn btn-danger role-delete']); ?></span>
            </div>
            <?php ActiveForm::end(); ?>
<?php
$saveRoleUrl = Url::to(['/offer/default/save-vehicle']);
$this->registerJs('
    $(".offervehicleform input").change(function(){
        form = $(this).closest("form");
        price = form.find("#offervehicle-price").val();
        duration = form.find("#offervehicle-distance").val();
        quantity = form.find("#offervehicle-quantity").val();
        total = price*duration*quantity;
        $(this).parent().parent().parent().find(".total-netto").empty().append(total);
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrl.'",
          data: form.serialize()
        });
    });


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
                    form.parent().find(".total-netto").empty().append(total);
                  }
                });
        }

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


    
    });'); ?>