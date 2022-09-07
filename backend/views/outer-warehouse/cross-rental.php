<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

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
                                    $eog = \common\models\EventOuterGear::find()->where(['outer_gear_id'=>$outerGear->id])->andWhere(['event_id'=>$event_id])->one();
                                    if (!$eog)
                                    {
                                        $model = \common\models\Event::findOne($event_id);
                                        $eog = new \common\models\EventOuterGear();
                                        $eog->event_id = $event_id;
                                        $eog->outer_gear_id = $outerGear->id;
                                        $eog->price = $outerGear->price;
                                        $eog->prod = $prod;
                                        $eog->reception_time = $model->getTimeStart();
                                        $eog->return_time = $model->getTimeEnd();
                                    }  
                                    
                                    $form = ActiveForm::begin(['id'=>'EOGForm'.$outerGear->id, 'options'=>['class'=>'form-outer', 'autocomplete'=>'off']]);
                                    echo $form->field($eog, 'outer_gear_id')->hiddenInput()->label(false);
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
                            ?>
                        </div>
                        </div>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <?= Html::a(Yii::t('app', 'Zapisz'), '#', ['class' => 'btn btn-success', 'onclick'=>'saveForm(); return false;']) ?>

        </div>
    </div>
</div>

<?php
    $link = Url::to("/admin/outer-warehouse/manage-outer-gear")."?id=".$event_id."&type=event";
?>
<script type="text/javascript">
var saved_forms = 0;
    function saveForm()
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
                            //tutaj rozwiązanie konfliktu
                            $("#outer_modal").modal("hide");
                            toastr.success("<?=Yii::t('app', 'Sprzęt dodany do eventu')?>");
                            resolveConflict($("#eventoutergear-quantity").val(), 0);
                    });
        });
    }

    function resolveConflict(newValue, oldValue){
        swal({
            title: "<?=Yii::t('app', 'Czy konflikt został rozwiązany?')?>",
            icon:"info",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            partial: {
                text:"<?=Yii::t('app', 'Częściowo')?>",
                value:"partial"
            },
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              location.href = "<?=Url::to(['warehouse/conflict', 'id'=>$conflict_id]);?>";
              break; 
            case "partial":
              location.href = "<?=Url::to(['warehouse/conflict-partial', 'id'=>$conflict_id]);?>&old="+oldValue+"&quantity="+newValue;
              break;       
          }
        });
    }
</script>





