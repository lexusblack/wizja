<?php
use kartik\daterange\DateRangePicker;
use yii\helpers\Inflector;
use Codeception\Util\ReflectionHelper;
?>


    <?php
    $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
    $calendarEvents = [

    ];
    $formatJs = 'DD/MM/YYYY HH:mm';
    $calendarOptions = [
        'timePicker'=>true,
        'timePickerIncrement'=>15,
        'timePicker24Hour' => true,
        
        'locale'=>['format'=>'d/m/Y H:i']
    ];

    $attributeName = 'packing';
    ?>
    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
        //        echo $form->field($model, $attributeName.'_type')->checkbox(['class'=>'dateRangeType', 'data'=>['target'=>'event-'.$attributeName.'daterange']]);
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => $attributeName.'DateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                'pluginOptions'=>array_merge($calendarOptions, ['linkedCalendars'=>false]),
            ]) . $addon;
        echo '</div>';
        ?>
    </div>

    <?php
    $attributeName = 'montage';
    ?>
    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
        //        echo $form->field($model, $attributeName.'_type')->checkbox(['class'=>'dateRangeType', 'data'=>['target'=>'event-'.$attributeName.'daterange']]);
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => $attributeName.'DateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                // 'startAttribute' => $attributeName.'_start',
                // 'endAttribute' => $attributeName.'_end',
                'pluginOptions'=>array_merge($calendarOptions, [
                    'minDate'=>$model->packing_end,
                ]),
                'pluginEvents' => array_merge($calendarEvents, [
                    'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
//                                eventDates("readiness", end);
//                                var attrs = ["montage", "readiness", "practice", "disassembly", "event"];
                         }',
                ]),
            ]) . $addon;
        echo '</div>';
        ?>
    </div>

    <?php
    $attributeName = 'readiness';
    ?>
    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
        //        echo $form->field($model, $attributeName.'_type')->checkbox(['class'=>'dateRangeType', 'data'=>['target'=>'event-'.$attributeName.'daterange']]);
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => $attributeName.'DateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                // 'startAttribute' => $attributeName.'_start',
                // 'endAttribute' => $attributeName.'_end',
                'pluginOptions'=>array_merge($calendarOptions, [
                    'minDate'=>$model->montage_end,
                ]),
                'pluginEvents' => array_merge($calendarEvents, [
                    'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
//                                eventDates("practice", end);
//                                var attrs = ["montage", "readiness", "practice", "disassembly", "event"];
                         }',
                ]),
            ]) . $addon;
        echo '</div>';
        ?>
    </div>

    <?php
    $attributeName = 'practice';
    ?>
    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
        //        echo $form->field($model, $attributeName.'_type')->checkbox(['class'=>'dateRangeType', 'data'=>['target'=>'event-'.$attributeName.'daterange']]);
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => $attributeName.'DateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                // 'startAttribute' => $attributeName.'_start',
                // 'endAttribute' => $attributeName.'_end',
                'pluginOptions'=>array_merge($calendarOptions, [
                    'minDate'=>$model->readiness_end,
                ]),
                'pluginEvents' => array_merge($calendarEvents, [
                    'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
//                                eventDates("event", end);
//                                var attrs = ["montage", "readiness", "practice", "disassembly", "event"];
                         }',
                ]),
            ]) . $addon;
        echo '</div>';
        ?>
    </div>


    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel('eventDateRange').'</label>';
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => 'eventDateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                // 'startAttribute' => 'event_start',
                // 'endAttribute' => 'event_end',
                'pluginOptions'=>array_merge($calendarOptions, [
                    'minDate'=>$model->practice_end,
                ]),
                'pluginEvents' => array_merge($calendarEvents, [
                    'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
//                                eventDates("disassembly", end);
//                                var attrs = ["montage", "readiness", "practice", "disassembly", "event"];
                         }',
                ]),
            ]) . $addon;
        echo '</div>';
        ?>
    </div>



    <?php
    $attributeName = 'disassembly';
    ?>
    <div class="form-group">
        <?php

        echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
        //        echo $form->field($model, $attributeName.'_type')->checkbox(['class'=>'dateRangeType', 'data'=>['target'=>'event-'.$attributeName.'daterange']]);
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
                'model'=>$model,
                'attribute' => $attributeName.'DateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                // 'startAttribute' => $attributeName.'_start',
                // 'endAttribute' => $attributeName.'_end',
                'pluginOptions'=>array_merge($calendarOptions, [
                    'minDate'=>$model->event_end,
                ]),
                'pluginEvents' => $calendarEvents,
            ]) . $addon;
        echo '</div>';
        ?>
    </div>

<?php
$base = Inflector::underscore(ReflectionHelper::getClassShortName($model));
$this->registerJs('
function eventDates(attr, start)
{
    var input = $("#'.$base.'-"+attr+"daterange");
    var el = input.closest(".input-group");
    var picker = el.data("daterangepicker");
    
    picker.setStartDate(start);
    picker.setEndDate(start);
    
    //var end = picker.endDate.format("YYYY-MM-DD HH:mm");
    var range = start + " - " + start;
    picker.minDate = picker.startDate;
 
    input.val(range);
    
//    console.log(picker);
}
');
