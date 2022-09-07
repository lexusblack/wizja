<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">

<div class="row">
    <div class="col-md-12">
        <div class="row">
        <?php $form = ActiveForm::begin(['id'=>'copyEventForm']); 
        echo $form->field($model, 'event_copy')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Event::getList(),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz event')],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
         ActiveForm::end(); ?>
        </div>
    <?php   ?>
    <?= Html::a(Yii::t('app', 'Zapisz'), ['copy-from-save', 'id'=>$model->event_to, 'type'=>$model->type], ['class' =>'btn btn-primary btn-sm submit-all']); ?>
</div>
</div>
</div>

<?php
$this->registerJs('

    $(".submit-all").click(function(e){
        e.preventDefault();
        $(this).attr("disabled", "disabled");
        //pobieramy id
        data = {event_copy:$("#copyfromeventform-event_copy").val()};
        $.post($(this).attr("href"), data, function(response){
            var modal = $("#copy_modal");
            modal.find(".modalContent").empty().append(response);
            var modal = $("#copy_modal_crew");
            modal.find(".modalContent").empty().append(response);
        });
        //robimy import
        //wypluwamy raport z update
        //window.location.reload();
    });

');