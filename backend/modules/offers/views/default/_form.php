<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\daterange\DateRangePicker;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $form yii\widgets\ActiveForm */


$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
?>
<div class="offer-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'enableClientScript' => false,
        'id'=>'offer-form'
    ]); ?>
    <?= $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-md-6">


            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            <?php if ($model->isNewRecord) {
                echo $form->field($model, 'event_type')->widget(Select2::classname(), [
                    'data' => \common\helpers\ArrayHelper::map(\common\models\ScheduleType::find()->asArray()->all(), 'id', 'name'),
                    'options' => [
                    'placeholder' => Yii::t('app', 'Schemat harmonogramu'),
                    ],
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                    ],
            
            ])->label(Yii::t('app', 'Schemat harmonogramu'));
            } ?>

            <?= $form->field($model, 'offer_draft_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(\common\models\OfferDraft::find()->asArray()->all(), 'id', 'name'),
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ]);?>

            <?= $form->field($model, 'price_group_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(\common\models\PriceGroup::find()->asArray()->all(), 'id', 'name'),
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ]);?>

            <?php
            echo $form->field($model, 'exchange_rate')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ])->label(Yii::t('app', 'Kurs (podaj w przypadku oferty w innej walucie'));
            ?>

            <?= $form->field($model, 'language')->dropDownList(['pl'=>'polski', 'en'=>'angielski']);?>

            <?php if (\common\models\Firm::getList()){
                echo $form->field($model, 'firm_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Firm::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Firma domyślna'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
                } ?>

            <?= $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => \common\models\Offer::getStatusList(),
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ]);?>

            <?= $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);?>

            <?= $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className());?>

            <?= $form->field($model, 'location_id')->widget(\common\widgets\LocationField::className()); ?>
            <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(Yii::t('app', 'lub wpisz adres')) ?>
            
            <?php echo $form->field($model, 'manager_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_SUPERADMIN]),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php
            echo $form->field($model, 'pm_cost_percent')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'pm_cost')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'payment_days')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>0,
                ]
            ]);
            ?>
            <label class="control-label"><?= Yii::t('app', 'Data sporządzenia ofery') ?></label>
            <?php
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'offer_date',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?><br>
            <?php echo $form->field($model, 'comment')->widget(\common\widgets\RedactorField::className())->label(Yii::t('app', 'Uwagi')); ?>
        </div>
        <div class="col-md-6">
        <?php if ($model->isNewRecord) { ?>
        <?php foreach (\common\models\ScheduleType::find()->all() as $type)
        { $key = $type->id; ?>
        <div id="schedule-<?=$key?>" class="schedule-form" style="display:none">
        <h1><?=Yii::t('app', 'Harmonogram')?></h1>
        <?php
            $schedules = [];
            $models = \common\models\Schedule::find()->where(['schedule_type_id'=>$type->id])->orderBy(['position'=>SORT_ASC])->all();
            foreach ($models as $m)
            {
                $schedules[$m->id]= new \common\models\OfferSchedule(); 
            }
            $schedule = new \common\models\form\ScheduleForm(['schedules'=>$schedules]);
            foreach ($models as $m)
            {
                if ($m->is_required)
                {
                    $r = " required";
                }else
                {
                    $r = "";
                }
                $baseIndex = 'schedules['.$m->id.']';
             echo $form->field($schedule, $baseIndex.'[dateRange]')->widget(\common\widgets\DateRangeField2::className(), ['options'=>['class'=>'form-control'.$r, 'autocomplete'=>'off']])->label($m->name);

                          }
            ?>
         </div>

        <?php    } ?>
        <?php    } ?>
       
            <br>
            <label class="control-label"><?= Yii::t('app', 'Oferta ważna do') ?></label>
            <?= DatePicker::widget([
                'model' => $model,
                'attribute' => 'term_to',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'linkedCalendars'=>false,
                ],
            ]);
            ?><br>
        </div>

            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
<?php if ($model->isNewRecord) {

    $this->registerJS('
        $("#offer-form").submit(function(e){
            var $return = true;
            $(".help-block").html("");
            $(".form-group").removeClass("has-error");
            //sprawdzamy pola obowiązkowe - nazwa i klient
            if ($("#offer-name").val()=="")
            {
                //nie ma wprowadzonej nazwy
                $("#offer-name").parent().addClass("has-error");
                $("#offer-name").parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                $return = false;
            }
            if ($("#offer-customer_id").val()=="")
            {
                //nie ma wprowadzonej nazwy
                $("#offer-customer_id").parent().addClass("has-error");
                $("#offer-customer_id").parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                $return = false;
            }
            /*
            $("#schedule-"+$("#offer-event_type").val()).find(".required").each(function(){
                if ($(this).val()=="")
                {
                    $(this).parent().addClass("has-error");
                    $(this).parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                    $return = false;
                }
            });
            */
            //sprawdzamy pola datowe zaznaczone w wybranym harmonogramie jako wymagane
            return $return;
        });
        ');
    } ?>

<?php $this->registerJS('
    var payments = '.json_encode($paymentsArray).'
    $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
        if (! confirm("'.Yii::t('app', "Czy na pewno usunąć ten model?").'")) {
            return false;
        }

        var val = $(item).find(".id_input").val();
        
        if(val && val !== ""){
            console.log($(item).find(".id_input").val());
            $.ajax("'.\yii\helpers\Url::toRoute(['/offer/timetable/delete','id'=>'']).'"+val, {
                type: "POST"
            }).done(function(data) {
                $(item).remove();
            }).error(function(data) {
                alert("'.Yii::t('app', "You have not permissions").'");
            });

           return false; 
        }
        return true;
        
    });

    $("#offer-customer_id").change(function(){
        if (payments[$(this).val()]!=null)
            $("#offer-payment_days").val(payments[$(this).val()]);
    });
    $("#schedule-"+$("#offer-event_type").val()).show();
    $("#offer-event_type").change(function(){
        $(".schedule-form").hide();
        $("#schedule-"+$("#offer-event_type").val()).show();
    });

'); ?>
