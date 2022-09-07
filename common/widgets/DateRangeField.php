<?php
namespace common\widgets;

use kartik\daterange\DateRangePicker;
use Yii;

class DateRangeField extends DateRangePicker
{
    public function init()
    {
        $this->useWithAddon = false;
        $this->pluginOptions = [
            'timePicker'=>true,
            'timePickerIncrement'=>5,
            'timePicker24Hour' => true,
            'linkedCalendars'=>false,
            'locale'=>[
                'format' => 'Y-m-d H:i',
                "applyLabel" => Yii::t('app', "Ok"),
                "cancelLabel" => Yii::t('app', "Anuluj"),
                "fromLabel" => Yii::t('app', "Od"),
                "toLabel" => Yii::t('app', "Do"),
                "customRangeLabel" => Yii::t('app', "Własna"),
                "weekLabel" => Yii::t('app', "T"),
                "daysOfWeek" => [
                    Yii::t('app', "Nd"),
                    Yii::t('app', "Pon"),
                    Yii::t('app', "Wt"),
                    Yii::t('app', "Śr"),
                    Yii::t('app', "Czw"),
                    Yii::t('app', "Pią"),
                    Yii::t('app', "Sob")
                ],
                "monthNames" => [
                    Yii::t('app', "Styczeń"),
                    Yii::t('app', "Luty"),
                    Yii::t('app', "Marzec"),
                    Yii::t('app', "Kwiecień"),
                    Yii::t('app', "Maj"),
                    Yii::t('app', "Czerwiec"),
                    Yii::t('app', "Lipiec"),
                    Yii::t('app', "Sierpień"),
                    Yii::t('app', "Wrzesień"),
                    Yii::t('app', "Październik"),
                    Yii::t('app', "Listopad"),
                    Yii::t('app', "Grudzień")
                ],
                "firstDay" => 1
            ],
        ];
        $this->convertFormat = true;
        $this->startAttribute = 'start_time';
        $this->endAttribute = 'end_time';

        if ($this->model->{$this->startAttribute} == null)
        {
            $this->model->{$this->startAttribute} = date('Y-m-d H');
        }
        if ($this->model->{$this->endAttribute} == null)
        {
            $this->model->{$this->endAttribute} = date('Y-m-d H');
        }
        parent::init();
    }
}