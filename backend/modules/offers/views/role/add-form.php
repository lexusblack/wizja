<?php
use kartik\form\ActiveForm;
use kartik\helpers\Html;
\common\assets\AreYouSureAsset::register($this);
use yii\helpers\Url;
$i=0;
?>
    <?php $form = ActiveForm::begin([
        'id' => 'offer-role-'.$role->role_id."-packing",
        'options'=>[
            'class'=>'offerroleform'
        ]
            ]); ?>
            <?php echo  $form->field($role, 'id')->hiddenInput()->label(false); ?>
            <?php echo  $form->field($role, 'offer_id')->hiddenInput()->label(false); ?>
            <?php echo  $form->field($role, 'time_type')->hiddenInput()->label(false); ?>
    
    <div class="col-xs-1" style="padding-top:6px;"></div>
    <div class="col-xs-3">
            <?php 
                $roles = \common\models\UserEventRole::getList();
                echo $form->field($role, 'role_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $roles,
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id'=>'offerrole-role-id-'.mktime(),
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
            <?php echo  $form->field($role, 'price')->textInput(['maxlength' => true, 'style'=>'padding-left:0px; padding-right:0px;'])->label(false); ?>
            </div><div class="col-xs-1">
            <?php echo  $form->field($role, 'quantity')->textInput(['maxlength' => true])->label(false); ?>
            </div><div class="col-xs-1">
            <?php echo  $form->field($role, 'duration')->textInput(['maxlength' => true])->label(false); ?>
            </div><div class="col-xs-1">
            <?php echo  $form->field($role, 'unit')->textInput(['maxlength' => true])->label(false); ?>
            </div><div class="col-xs-2 no-padding">
            <?php echo  $form->field($role, 'role_price_id')->dropDownList(\common\models\RolePrice::getList($role->role_id, $model->priceGroup->currency), ['data-period'=>$model->getPeriodTime($role->time_type)])->label(false); ?>
            </div><div class="col-xs-2" style="padding-top:6px; padding-left:10px;">
            <div class="total-netto pull-left"><?=$role->price*$role->quantity*$role->duration?></div>
            <span class="pull-right"><?php echo Html::a(Html::icon('trash'), ['/offer/default/delete-role', 'id'=>$role->id], ['class'=>'btn-xs btn btn-danger role-delete']); ?></span>
            </div>
            
            <?php ActiveForm::end(); ?>
<?php
$saveRoleUrl = Url::to(['role/save']);
$this->registerJs('

    $(".offerroleform input").unbind("change");
    $(".offerroleform select").unbind("change");
     $(".offerroleform select").unbind("focusin");
     $(".role-delete").unbind("click");
    $(".offerroleform input").change(function(){
        form = $(this).closest("form");
        price = form.find("#offerrole-price").val();
        duration = form.find("#offerrole-duration").val();
        quantity = form.find("#offerrole-quantity").val();
        total = price*duration*quantity;
        $(this).parent().parent().parent().find(".total-netto").empty().append(total);
        $.ajax({
          type: "POST",
          url: "'.$saveRoleUrl.'",
          data: form.serialize()
        });
    });


$(".offerroleform select").on("focusin", function(){
    console.log("Saving value " + $(this).val());
    $(this).data("val", $(this).val());
})

    $(".offerroleform select").change(function(){
        if ($(this).hasClass("oferrole"))
        {
                form = $(this).closest("form");
                old_val = $(this).data("val");
                $(this).data("val", $(this).val());                
                $.ajax({
                  type: "POST",
                  url: "'.$saveRoleUrl.'?old="+old_val,
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
                    form.find("#offerrole-price").val(result.price);
                    form.find("#offerrole-role_price_id").val(result.role_price_id);
                    price = form.find("#offerrole-price").val();
                    duration = form.find("#offerrole-duration").val();
                    quantity = form.find("#offerrole-quantity").val();
                    total = price*duration*quantity;
                    form.parent().find(".total-netto").empty().append(total);
                  }
                });
        }

    });

    $(".offerroleform select").each(function(){
        $(this).data("val", $(this).val());
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