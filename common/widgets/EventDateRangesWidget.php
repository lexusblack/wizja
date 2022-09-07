<?php
namespace common\widgets;

use kartik\daterange\DateRangePicker;
use yii\base\Widget;
use yii\helpers\Inflector;
use Codeception\Util\ReflectionHelper;

class EventDateRangesWidget extends Widget
{
    public $model;

    public function run()
    {
        parent::run();
//        renderowanie rozpierdala wigdety CustomerField, ContactField, LocationField w offer/_form (w złym miejscu renderowane)
//        
//        return $this->render('eventDateRangesWidget', [
//            'model'=>$this->model,
//        ]);
        $model = $this->model;

        $addon = <<< HTML
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-calendar"></i>
            </span>
HTML;

        $formatJs = 'DD/MM/YYYY HH:mm';
        $calendarEvents = [];
        $calendarOptions = [
            'timePicker'=>true,
            'timePickerIncrement'=>5,
            'timePicker24Hour' => true,
            'linkedCalendars'=>false,
            'locale'=>['format'=>'d/m/Y H:i']
        ];

        $attributeName = 'packing';
        ?>
        <div class="form-group">
            <?php

            echo '<label class="control-label">'.$model->getAttributeLabel($attributeName.'DateRange').'</label>';
            echo '<div class="input-group drp-container">';
            echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute' => $attributeName.'DateRange',
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>array_merge($calendarOptions, []),
                    'pluginEvents' => array_merge($calendarEvents, [
                        'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
                         }',
                    ]),
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
            echo '<div class="input-group drp-container">';
            echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute' => $attributeName.'DateRange',
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>array_merge($calendarOptions),
                    'pluginEvents' => array_merge($calendarEvents, [
                        'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
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
            echo '<div class="input-group drp-container">';
            echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute' => $attributeName.'DateRange',
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>array_merge($calendarOptions),
                    'pluginEvents' => array_merge($calendarEvents, [
                        'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
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
                    'pluginOptions'=>array_merge($calendarOptions),
                    'pluginEvents' => array_merge($calendarEvents, [
                        'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
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
                    'pluginOptions'=>array_merge($calendarOptions),
                    'pluginEvents' => array_merge($calendarEvents, [
                        'apply.daterangepicker' => 'function(event, picker){
                                var end = picker.endDate.format("'.$formatJs.'");
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
            echo '<div class="input-group drp-container">';
            echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute' => $attributeName.'DateRange',
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'pluginOptions'=>array_merge($calendarOptions),
                    'pluginEvents' => $calendarEvents,
                ]) . $addon;
            echo '</div>';
            ?>
        </div>

        <?php
        $base = Inflector::underscore(ReflectionHelper::getClassShortName($model));
        $this->view->registerJs('
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
            }
        ');
    }
}



