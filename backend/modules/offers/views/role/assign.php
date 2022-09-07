<?php
use kartik\form\ActiveForm;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
\common\assets\AreYouSureAsset::register($this);
use yii\helpers\Url;
use kartik\widgets\Select2;

/* @var $model \common\models\Offer */

$this->title = Yii::t('app', 'Dodaj obsługę');
$this->params['breadcrumbs'][] = ['label' => 'Oferty', 'url' => ['/offer/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/offer/default/view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
$schedule_list = [];
foreach ($model->offerSchedules as $schedule){
    $schedule_list[$schedule->id] = $schedule->name;
    }
?>
<script type="text/javascript">
    var roles = [];
    var rolesHours = [];
    <?php foreach ($roles as $key=>$val){
        if ($val)
            echo "roles[".$key."]=".$val.";";
        else
            echo "roles[".$key."]=0;";
    }?>
    <?php foreach ($rolesHour as $key=>$val){
        if ($val)
            echo "rolesHours[".$key."]=".$val.";";
        else
            echo "rolesHours[".$key."]=0;";
    }?>
</script>
<div class="offer-role-add-form">
<div class="ibox float-e-margins">
    <?php echo Html::a(Html::icon('arrow-left')." ".$model->name, ['/offer/default/view', 'id'=>$model->id], ['class'=>'btn btn-primary btn-sm']); ?>
    <?= Html::dropDownList('schema_id', null,
      ArrayHelper::map(\common\models\OfferRoleSchema::find()->where(['user_id'=>\Yii::$app->user->id])->all(), 'id', 'name'),['class'=>'form-control','style'=>'width:300px; display:inline-block', 'id'=>'offer-role-schema-id']) ?>
    <?php echo Html::a(Yii::t('app', 'Załaduj szablon'), ['#'], ['class'=>'btn btn-success btn-sm', 'onclick'=>'loadSchema(); return false;']); ?>
    <?php echo Html::a(Yii::t('app', 'Zapisz szablon'), ['#'], ['class'=>'btn btn-success btn-sm', 'onclick'=>'saveSchema(); return false;']); ?>

</div>
<?php foreach ($model->offerSchedules as $schedule){ ?>
<div class="ibox float-e-margins">
    <div class="ibox-title newsystem-bg" style="height:60px;">
    <div class="col-xs-6">
        <h5><?php echo $schedule->name." ".$schedule->getPeriodTime()."h"; ?></h5>
        </div>
    <div class="col-xs-2">
        <?= Html::a("<i class='fa fa-copy'></i> ".Yii::t('app', 'Kopiuj z'), ['/offer/role/copy', 'time_type'=>$schedule->id, 'offer_id'=>$model->id], ['class'=>'btn btn-xs pull-right white-button copy-schedule-button btn-primary', 'data-schedule'=>$schedule->id]); ?>
    </div>
    <div class="col-xs-4">
        <?= Select2::widget([
                                'data' => $schedule_list,
                                'name' => 'roles-'.$schedule->id,
                                'id' => 'select-schedules-ajax'.$schedule->id,
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz etap...'),
                                    'id'=>'select-schedules-ajax'.$schedule->id,
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => false,
                                ],
                            ]); ?>
    </div>
    </div>
    <div class="ibox-content">
    <div class="role-list">
    <div class="row">
    <div class="col-xs-1">#</div><div class="col-xs-3"><?=Yii::t('app', 'Nazwa')?></div><div class="col-xs-1"><?=Yii::t('app', 'Cena')?></div><div class="col-xs-1"><?=Yii::t('app', 'Liczba')?></div><div class="col-xs-1"><?=Yii::t('app', 'Okres')?></div><div class="col-xs-1"><?=Yii::t('app', 'jedn.')?></div><div class="col-xs-2"><?=Yii::t('app', 'Stawka')?></div><div class="col-xs-2"><?=Yii::t('app', 'Razem netto')?></div></div>
    <?php $i=1; foreach ($model->getORoles($schedule->id) as $role) { ?>
    <div class="row">
        <?php $form = ActiveForm::begin([
        'id' => 'offer-role-'.$role->id."-s".$schedule->id,
        'options'=>[
            'class'=>'offerroleform'
        ]
            ]); ?>
            <?php echo  $form->field($role, 'id')->hiddenInput()->label(false); ?>
            <?php echo  $form->field($role, 'offer_id')->hiddenInput()->label(false); ?>
            <?php echo  $form->field($role, 'time_type')->hiddenInput()->label(false); ?>
    
    <div class="col-xs-1" style="padding-top:6px;"><?=$i?>.</div>
    <div class="col-xs-3">
            <?php echo $form->field($role, 'role_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\UserEventRole::getList(),
                
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id'=>'offerrole-role-id-'.$role->id."-s".$schedule->id,
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
            <?php echo  $form->field($role, 'role_price_id')->dropDownList(\common\models\RolePrice::getList($role->role_id, $model->priceGroup->currency), ['data-period'=>$schedule->getPeriodTime()])->label(false); ?>
            </div><div class="col-xs-2" style="padding-top:6px; padding-left:10px;">
            <div class="total-netto pull-left"><?=$role->price*$role->quantity*$role->duration?></div>
            <span class="pull-right"><?php echo Html::a(Html::icon('trash'), ['/offer/default/delete-role', 'id'=>$role->id,], ['class'=>'btn-xs btn btn-danger role-delete']); ?></span>
            </div>
            <?php ActiveForm::end(); ?>
            </div>
    <?php $i++; } ?>
    </div>
    <?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/offer/role/add-form', 'time_type'=>$schedule->id, 'offer_id'=>$model->id], ['class'=>'btn btn-primary btn-sm add-role']); ?>    
    </div>
</div>
<?php } ?>

</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function saveSchema()
    {
        swal({
          text: 'Podaj nazwę schematu',
          content: {
            element: "input",
            attributes: {
              placeholder: "Podaj nazwę",
              type: "text",
            }
        },
          button: {
            text: "OK",
            closeModal: true,
          },
        })
        .then(name => {
          if (!name) name="schemat1";
            x = name;
            url = "<?=Url::to('/admin/offer/role/save-schema?offer_id='.$model->id.'&name=')?>"+x;
            $.ajax({url: url, success: function(result){
                toastr.success('<?=Yii::t('app', 'Zapisano!')?>')
            }});
        });
    }
    function loadSchema()
    {
        val = $("#offer-role-schema-id").val();
        location.href = "<?=Url::to('/admin/offer/role/load-schema?offer_id='.$model->id.'&schema=')?>"+val;
    }
</script>
<?php
$saveRoleUrl = Url::to(['role/save']);
$this->registerJs('
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
                    //alert(result);
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
                    form.find("#offerrole-unit").val(result.unit);
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

    $(".add-role").click(function(e){
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

    $(".copy-schedule-button").click(function(e){
        e.preventDefault();
        schedule_id = $(this).data("schedule");
        id = $("#select-schedules-ajax"+schedule_id).val();
        if (id)
            location.href = $(this).attr("href")+"&schedule_from="+$("#select-schedules-ajax"+schedule_id).val();
        else
            alert("Wybierz etap do skopiowania");
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