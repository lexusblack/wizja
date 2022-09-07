<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */
$packlist_schemas = \common\helpers\ArrayHelper::map(\common\models\PacklistSchema::find()->asArray()->all(), 'id', 'name');
$pack = \common\models\PacklistSchema::find()->one();

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
?>
<div class="event-form">
    <?php $form = ActiveForm::begin([
        'id' => 'event-form',
        'enableAjaxValidation' => false,
        'enableClientScript' => false,
    ]); ?>
    <?php
        echo $form->errorSummary($model);
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
                        <?php 
                        if ($model->isNewRecord) {
                            if (($event)||($offer))
                                $model->schedule_type = null;
                echo $form->field($model, 'schedule_type')->widget(\kartik\widgets\Select2::classname(), [
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
            }
if ($model->isNewRecord) {
if ($packlist_schemas)
{
    $model->packlist_schema = $pack->id;
    echo $form->field($model, 'packlist_schema')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => $packlist_schemas,
                    'options' => [
                    'placeholder' => Yii::t('app', 'Grupy sprzętowe'),
                    ],
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                    ],
            
            ])->label(Yii::t('app', 'Grupy sprzętowe'));
} }
             ?>

            <?php echo $form->field($model, 'type')->dropDownList(\common\models\Event::getTypeList()); ?>
            <?php echo $form->field($model, 'event_type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Event::getEventTypeList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php if (Yii::$app->params['companyID']=="admin") { ?>
            <?php echo $form->field($model, 'project_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => ArrayHelper::map(\common\models\Project::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            echo $form->field($model, 'description')->widget(\common\widgets\RedactorField::className());
            echo $form->field($model, 'userIds')->widget(\kartik\widgets\Select2::className(), [
                    'data' => \common\models\User::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ])->label(Yii::t('app', 'Przypisani użytkownicy'));
            
            ?>
            <?php } ?>
            <?php echo $form->field($model, 'location_id')->widget(\common\widgets\LocationField::className())
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"'); ?>
            <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'autocomplete'=>"off", 'placeholder'=>Yii::t('app', 'Adres imprezy ręcznie')]) ?>
            <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>

            <?php
            echo $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className())
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>

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

            <?php echo $form->field($model, 'level')->dropDownList(\common\models\Event::getLevelList()); ?>

            <?php echo $form->field($model, 'departmentIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Department::getModelList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
            <?php 
            if (Yii::$app->params['companyID']!="imagination") {
            if ($schema_change_possible)
            echo $form->field($model, 'tasks_schema_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TasksSchema::getList('event'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            }
            ?>
            <?php echo $form->field($model, 'info')->widget(\common\widgets\RedactorField::className()); ?>
            <?php echo $form->field($model, 'details')->widget(\common\widgets\RedactorField::className()); ?>
        </div>
        <div class="col-md-6">
                <?php if ($model->isNewRecord) { ?>
        <?php

        if ($event)
        {
            ?>
            <div id="schedule-0" class="schedule-form">
            <h1><?=Yii::t('app', 'Harmonogram')?></h1>
            <?php
            foreach ($event->eventSchedules as $s)
            {
                $schedules[$s->id] = new \common\models\EventSchedule(); 
                $schedules[$s->id]->attributes = $s->attributes;
                if ($s->start_time)
                    $schedules[$s->id]->dateRange = $s->start_time." - ".$s->end_time;

                $schedule = new \common\models\form\ScheduleForm(['schedules'=>$schedules]);
                    if ($s->is_required)
                    {
                        $r = " required";
                    }else
                    {
                        $r = "";
                    }
                    $baseIndex = 'schedules['.$s->id.']';
                 echo $form->field($schedule, $baseIndex.'[dateRange]')->widget(\common\widgets\DateRangeField2::className(), ['options'=>['class'=>'form-control'.$r, 'autocomplete'=>'off', 'value'=>$schedules[$s->id]->dateRange]])->label($s->name);
            } ?>
            </div>
            <?php
        }

        ?>
        <?php
        if ($offer)
        {
            ?>
            <div id="schedule-0" class="schedule-form">
            <h1><?=Yii::t('app', 'Harmonogram')?></h1>
            <?php
            foreach ($offer->offerSchedules as $s)
            {
                $schedules[$s->id] = new \common\models\EventSchedule(); 
                $schedules[$s->id]->attributes = $s->attributes;
                if($s->start_time)
                    $schedules[$s->id]->dateRange = $s->start_time." - ".$s->end_time;
                $schedule = new \common\models\form\ScheduleForm(['schedules'=>$schedules]);
                    if ($s->is_required)
                    {
                        $r = " required";
                    }else
                    {
                        $r = "";
                    }
                    $baseIndex = 'schedules['.$s->id.']';
                 echo $form->field($schedule, $baseIndex.'[dateRange]')->widget(\common\widgets\DateRangeField2::className(), ['options'=>['class'=>'form-control'.$r, 'autocomplete'=>'off', 'value'=>$schedules[$s->id]->dateRange]])->label($s->name);
            } ?>
            </div>
            <?php
        }

        ?>
        <?php foreach (\common\models\ScheduleType::find()->all() as $type)
        { $key = $type->id; ?>
        <div id="schedule-<?=$key?>" class="schedule-form" style="display:none">
        <h1><?=Yii::t('app', 'Harmonogram')?></h1>
        <?php

            $schedules = [];
            $models = \common\models\Schedule::find()->where(['schedule_type_id'=>$type->id])->orderBy(['position'=>SORT_ASC])->all();
            foreach ($models as $m)
            {
                $schedules[$m->id]= new \common\models\EventSchedule(); 
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

            <?= $form->field($model, 'code')->textInput(['maxlength' => true])->hint(Yii::t('app', 'Jeśli nie wypełnisz, ID zostanie wygenerowane.')) ?>
            <?php echo $form->field($model, 'paying_date')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Event::getPayingDateList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php echo  $form->field($model, 'route_start')->textInput(['maxlength' => true])->hint(Yii::t('app', 'Wypełnij, jeśli inne niż adres firmy.')); ?>
            <?php echo  $form->field($model, 'route_end')->textInput(['maxlength' => true])->hint(Yii::t('app', 'Wypełnij, jeśli inne niż adres Miejsca.')); ?>



        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
            </div>
        </div>
    </div>














    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
var full_types = [];
<?php
foreach (\common\models\EventModel::find()->all() as $e)
{
    if ($e->type==1)
    {
        echo "full_types[".$e->id."] = 1; ";
    }else{
        echo "full_types[".$e->id."] = 0; ";
    }
}
?>
</script>
<?php if ($model->isNewRecord) {

    $this->registerJS('
        $("#event-form").submit(function(e){
            var $return = true;
            $(".help-block").html("");
            $(".form-group").removeClass("has-error");
            //sprawdzamy pola obowiązkowe - nazwa i klient
            if ($("#event-name").val()=="")
            {
                //nie ma wprowadzonej nazwy
                $("#event-name").parent().addClass("has-error");
                $("#event-name").parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                $return = false;
            }
            if ($("#event-customer_id").val()=="")
            {
                //nie ma wprowadzonej nazwy
                $("#event-customer_id").parent().addClass("has-error");
                $("#event-customer_id").parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                $return = false;
            }
            $("#schedule-"+$("#event-schedule_type").val()).find(".required").each(function(){
                if ($(this).val()=="")
                {
                    $(this).parent().addClass("has-error");
                    $(this).parent().find(".help-block").html("'.Yii::t('app', 'Pole nie może pozostać bez wartości').'")
                    $return = false;
                }
            });
            //sprawdzamy pola datowe zaznaczone w wybranym harmonogramie jako wymagane
            return $return;
        });
        ');
    } ?>
<?php
$this->registerJs('


function setMinAndMaxDatesForPicker(picker,key) {

    if(window.range_dates.hasOwnProperty(key)){
    	if(window.range_dates[key].hasOwnProperty("minDate")){
            picker.setMinDate(window.range_dates[key]["minDate"]);
            if(picker.endDate && picker.startDate && picker.startDate.valueOf() < picker.minDate.valueOf()){
				picker.setStartDate(window.range_dates[key]["minDate"]);
            }
            if(picker.endDate && picker.startDate && picker.endDate.valueOf() < picker.minDate.valueOf()){
				picker.setEndDate(window.range_dates[key]["minDate"]);
            }
        }

        if(window.range_dates[key].hasOwnProperty("maxDate")){
            picker.setMaxDate(window.range_dates[key]["maxDate"]);
            if(picker.endDate && picker.startDate && picker.endDate.valueOf() > picker.maxDate.valueOf()){
				picker.setEndDate(window.range_dates[key]["maxDate"]);
            }
            if(picker.endDate && picker.startDate && picker.startDate.valueOf() > picker.maxDate.valueOf()){
				picker.setStartDate(window.range_dates[key]["maxDate"]);
            }
        }
        
    }
}
    
    $("#event-schedule_type").change(function(){
        $(".schedule-form").hide();
        $("#schedule-"+$("#event-schedule_type").val()).show();
    });

');

if ((!$event)&&(!$offer))
    $this->registerJs('
        $("#schedule-"+$("#event-schedule_type").val()).show();');

