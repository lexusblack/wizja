<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Schedule */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="schedule-form">
    <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
        'enableClientScript' => false,
        'id'=>'schedule-form']); 
    $schedules = [];
    foreach ($models as $m)
    {
        $schedules[$m->id]= new \common\models\OfferSchedule(); 
    }
    $schedule = new \common\models\form\ScheduleForm(['schedules'=>$schedules]); ?>
<?php foreach ($models as $m)
{

    $baseIndex = 'schedules['.$m->id.']';
    ?>

    <?php echo $form->field($schedule, $baseIndex.'[dateRange]')->widget(\common\widgets\DateRangeField2::className(['startAttribute' =>$baseIndex.'[start_time]', 'endAttribute' =>$baseIndex.'[end_time]']))->label($m->name); ?>


<?php } ?>
    <?php ActiveForm::end(); ?>
</div>
