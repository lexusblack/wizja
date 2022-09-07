 <?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use common\helpers\Url;

 ?>


<?php if (Yii::$app->session->get('company')==1) { ?>
    <div style="width:100%;  overflow-x:scroll;">
    <div  style="width:1200px;">
 <div class="row" style='font-weight:bold;'>

                                <div class="col-md-2"><?= Yii::t('app', 'Nazwa firmy') ?></div>
                                <div class="col-md-1"><?= Yii::t('app', 'Cena wypożyczenia') ?></div>
                                <div class="col-md-1"><?= Yii::t('app', 'Liczba sztuk') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Uwagi') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Data odbioru') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Data zwrotu') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Odpowiedzialny') ?></div>

</div>
							<?php
							foreach ($outerGears as $outerGear)
							{
                                    if ($type=='event')
                                    {
                                      $eog = \common\models\EventOuterGear::find()->where(['outer_gear_id'=>$outerGear->id])->andWhere(['event_id'=>$model->event_id])->one();
                                    if (!$eog)
                                    {
                                        $eog = new \common\models\EventOuterGear();
                                        $eog->event_id = $model->event_id;
                                        $eog->outer_gear_id = $outerGear->id;
                                        $eog->price = $outerGear->price;
                                        $eog->prod = $prod;
                                        $eog->reception_time = $model->event->getTimeStart();
                                        $eog->return_time = $model->event->getTimeEnd();
                                    }  
                                }else{
                                    $eog = \common\models\RentOuterGear::find()->where(['outer_gear_id'=>$outerGear->id])->andWhere(['rent_id'=>$model->rent_id])->one();
                                    if (!$eog)
                                    {
                                        $eog = new \common\models\RentOuterGear();
                                        $eog->rent_id = $model->rent_id;
                                        $eog->outer_gear_id = $outerGear->id;
                                        $eog->price = $outerGear->price;
                                        $eog->reception_time = $model->rent->getTimeStart();
                                        $eog->return_time = $model->rent->getTimeEnd();
                                    }
                                }
                                    
								    $form = ActiveForm::begin(['id'=>'EOGForm'.$outerGear->id, 'options'=>['class'=>'form-outer', 'autocomplete'=>'off']]);
                                    echo $form->field($eog, 'outer_gear_id')->hiddenInput()->label(false);
                                   if ($type=='event')
                                        echo $form->field($eog, 'prod')->hiddenInput()->label(false);
                                ?>
                            <div class="row">
							<div class="col-md-2"><?=$outerGear->company->name?></div>
							<div class="col-md-1"><?= $form->field($eog, 'price')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false) ?></div>
							<div class="col-md-1"><?= $form->field($eog, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false) ?></div>
                            <div class="col-md-2"><?= $form->field($eog, 'description')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false) ?></div>
                            <div class="col-md-2"><?= $form->field($eog, 'reception_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                                    'saveFormat' => 'php:Y-m-d H:i:s',
                                    'ajaxConversion' => true,
                                    'options' => [
                                    'id'=>'reception'.$outerGear->id,
                                        'pluginOptions' => [
                                            'placeholder' => Yii::t('app', 'Wybierz datę zwrotu'),
                                            'autoclose' => true,
                                        ]
                                    ],
                                ])->label(false); ?></div>
                            <div class="col-md-2"><?= $form->field($eog, 'return_time')->widget(\kartik\datecontrol\DateControl::classname(), [
                                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                                    'saveFormat' => 'php:Y-m-d H:i:s',
                                    'ajaxConversion' => true,
                                    'options' => [
                                    'id'=>'return'.$outerGear->id,
                                        'pluginOptions' => [
                                            'placeholder' => Yii::t('app', 'Wybierz datę zwrotu'),
                                            'autoclose' => true,
                                        ]
                                    ],
                                ])->label(false); ?></div>
                            <div class="col-md-2">            <?php echo $form->field($eog, 'user_id')->widget(\kartik\widgets\Select2::className(), [
                                                'data' => \common\models\User::getList(),
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Wybierz...'),
                                                    'id'=>'user'.$outerGear->id,
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'multiple' => false,
                                                ],
                                            ])->label(false);
                                            ?></div>
							</div>
								<?php
                                ActiveForm::end(); 
							}
							?>
                        </div>
                        </div>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <?= Html::a(Yii::t('app', 'Zapisz'), '#', ['class' => 'btn btn-success', 'onclick'=>'saveForms(); return false;']) ?>

        </div>
    </div>
</div>
<?php 
if (($crn['cw'])||($crn['cw2'])){ ?>
<div class="row">
<div class="col-md-12" style="overflow-y:scroll; height:300px;">
<table class="table">
<tr style="background-color:#273a4a; color:white;"><td colspan=5><?=Yii::t('app', 'Podobny sprzęt w Cross Rental Network w pobliżu Twojego magazynu')?></td></tr>
<tr>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Liczba sztuk')?></th>
    <th><?=Yii::t('app', 'Firma')?></th>
    <th></th>
</tr>
<?php foreach ($crn['cw'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental2', 'id'=>$cr->id, 'event_id'=>$model->event_id, 'gear_id'=>$model->outer_gear_model_id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>

</tr>
<?php    }?>
<?php foreach ($crn['cw2'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
    <td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental2', 'id'=>$cr->id, 'event_id'=>$model->event_id, 'gear_id'=>$model->outer_gear_model_id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
</table>
</div>
</div>
<?php    } ?>

<?php if (($crn['ce'])||($crn['ce2'])){ ?>
<div class="row">
<div class="col-md-12" style="overflow-y:scroll; height:300px;">
<table class="table">
<tr style="background-color:#273a4a; color:white;"><td colspan=5><?=Yii::t('app', 'Podobny sprzęt w Cross Rental Network w pobliżu miejsca eventu')?></td></tr>
<tr>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Liczba sztuk')?></th>
    <th><?=Yii::t('app', 'Firma')?></th>
    <th></th>
</tr>
<?php foreach ($crn['ce'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
<td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental2', 'id'=>$cr->id, 'event_id'=>$model->event_id, 'gear_id'=>$model->outer_gear_model_id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
<?php foreach ($crn['ce2'] as $cr){ ?>
<tr>
    <td><?=$cr->gearModel->name?></td>
    <td><?=$cr->quantity?></td>
    <td><?=$cr->owner_name. "<br/>".$cr->owner_address." ".$cr->owner_city."<br/>".Yii::t('app', "tel").". ".$cr->owner_phone."<br/>".$cr->owner_mail?></td>
    <td><?=Html::a('Wyślij zapytanie', ['/chat/createcrn', 'id'=>$cr->id], ['class'=>['btn btn-sm btn-primary send-crn-request']])?></td>
<td><?=Html::a('Zarezerwuj', ['/outer-warehouse/cross-rental2', 'id'=>$cr->id, 'event_id'=>$model->event_id, 'gear_id'=>$model->outer_gear_model_id], ['class'=>['btn btn-sm btn-info crn-book']])?></td>
</tr>
<?php    }?>
</table>
</div>
</div>
<?php    } ?>

<?php $this->registerJs('
    $(".send-crn-request").click(function(e)
    {
        e.preventDefault();
        $.get($(this).attr("href"), function(data){
                openMessageDialog(data.id, 2);
            }); 
        $("#outer_modal").modal("hide");
    })
    '); 

$this->registerJs('
    $(".crn-book").click(function(e)
    {
        e.preventDefault();
        var modal = $("#outer_modal");
        var $link=$(this).attr("href");
        modal.modal("show");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("<?=$spinner?>");
        modal.find(".modalContent").load($link); 
    })
    ');
    ?>
<?php }else{ ?>
 <div class="row" style='font-weight:bold;'>

                                <div class="col-md-4"><?= Yii::t('app', 'Nazwa firmy') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Cena') ?></div>
                                <div class="col-md-1"><?= Yii::t('app', 'Liczba') ?></div>
                                <div class="col-md-3"><?= Yii::t('app', 'Uwagi') ?></div>
                                <div class="col-md-2"><?= Yii::t('app', 'Odpowiedzialny') ?></div>

</div>
                            <?php
                            foreach ($outerGears as $outerGear)
                            {
                                    if ($type=='event')
                                    {
                                      $eog = \common\models\EventOuterGear::find()->where(['outer_gear_id'=>$outerGear->id])->andWhere(['event_id'=>$model->event_id])->one();
                                    if (!$eog)
                                    {
                                        $eog = new \common\models\EventOuterGear();
                                        $eog->event_id = $model->event_id;
                                        $eog->outer_gear_id = $outerGear->id;
                                        $eog->price = $outerGear->price;
                                    }  
                                }else{
                                    $eog = \common\models\RentOuterGear::find()->where(['outer_gear_id'=>$outerGear->id])->andWhere(['rent_id'=>$model->rent_id])->one();
                                    if (!$eog)
                                    {
                                        $eog = new \common\models\RentOuterGear();
                                        $eog->rent_id = $model->rent_id;
                                        $eog->outer_gear_id = $outerGear->id;
                                        $eog->price = $outerGear->price;
                                    }
                                }
                                    
                                    $form = ActiveForm::begin(['id'=>'EOGForm'.$outerGear->id, 'options'=>['class'=>'form-outer', 'autocomplete'=>'off']]);
                                    echo $form->field($eog, 'outer_gear_id')->hiddenInput()->label(false);
                                ?>
                            <div class="row">
                            <div class="col-md-4"><?=$outerGear->company->name?></div>
                            <div class="col-md-2"><?= $form->field($eog, 'price')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false) ?></div>
                            <div class="col-md-1"><?= $form->field($eog, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off", 'value'=>1])->label(false) ?></div>
                            <div class="col-md-3"><?= $form->field($eog, 'description')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false) ?></div>
                            <div class="col-md-2">            <?php echo $form->field($eog, 'user_id')->widget(\kartik\widgets\Select2::className(), [
                                                'data' => \common\models\User::getList(),
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Wybierz...'),
                                                    'id'=>'user'.$outerGear->id,
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'multiple' => false,
                                                ],
                                            ])->label(false);
                                            ?></div>
                            </div>
                                <?php
                                ActiveForm::end(); 
                            }
                            ?>

<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <?= Html::a(Yii::t('app', 'Zapisz'), '#', ['class' => 'btn btn-success', 'onclick'=>'saveForms(); return false;']) ?>

        </div>
    </div>
</div>
<?php } ?>
<?php
if ($type=='event')
    $link = Url::to("/admin/outer-warehouse/manage-outer-gear")."?id=".$model->event_id."&type=event";
else
    $link = Url::to("/admin/outer-warehouse/manage-outer-gear")."?id=".$model->rent_id."&type=rent";
?>
<script type="text/javascript">
var total_forms = <?=count($outerGears)?>; 
var saved_forms = 0;
    function saveForms()
    {
        $(".form-outer").each(function(){
            form = $(this);
            formData = form.serialize(); 
            $post_link = '<?=$link?>';
                $.ajax({
                        url:$post_link, 
                        type:'POST',
                        data: formData,
                    })
                    .done(function(data){
                            saved_forms++;
                            if (total_forms<=saved_forms)
                            {
                                window.location.reload();
                            }
                    });
        });
    }
</script>