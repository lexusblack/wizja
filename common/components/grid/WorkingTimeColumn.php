<?php
namespace common\components\grid;

use kartik\grid\EditableColumn;
use Yii;
use yii\bootstrap\Html;
use yii\web\HttpException;
use kartik\editable\Editable;

class WorkingTimeColumn extends EditableColumn
{
    public $parentModel;
    public $type;
    public $connectionClassName;

    public $itemIdAttribute;
    public $parentIdAttribute = 'event_id';

    public function init()
    {
        if ($this->parentModel ===  null)
        {
            throw new HttpException(400, Yii::t('app','parentModel nie ustawiony!'));
        }
        $this->editableOptions = function ($item, $key, $index) {
            $model = $this->parentModel;
            $workingTime = $item->getWorkingTime($model->id,true);

            $connectionClassName = $this->connectionClassName;
            $m = $connectionClassName::findOne([$this->itemIdAttribute=>$item->id, $this->parentIdAttribute=>$model->id]);
            $m->dateRange = $workingTime;
            $type = $this->type;
            return [
                'formOptions' => [
                    'action'=>['event/update-working-time', 'eventId'=>$model->id, 'type'=>$type],
                ],
                'asPopover'=>true,
                'placement'=>\kartik\popover\PopoverX::ALIGN_LEFT,
                'inputType'=>Editable::INPUT_DATE_RANGE,
                'header'=>Yii::t('app','Czas pracy'),
                'size'=>\kartik\popover\PopoverX::SIZE_LARGE,
                'model'=>$m,
                'attribute' => 'dateRange',
                'submitButton'=>[
                    'icon' => Html::icon('ok'),
                    'class'=>'btn btn-sm btn-primary'
                ],
                'options'=> [

                    'id'=>$type.'_edit-'.$item->id,
                    'options'=>[
                        'style'=>'width: 100%',
                        'id'=>$type.'_picker-'.$item->id,
                        'class'=>'form-controll'
                    ],
                    'convertFormat'=>true,
                    'startAttribute' => 'start_time',
                    'endAttribute' => 'end_time',
                    'pluginOptions'=>[
                        'timePicker'=>true,
                        'timePickerIncrement'=>5,
                        'timePicker24Hour' => true,
                        'locale'=>['format' => 'Y-m-d H:i'],
                    ],
                ],
            ];
        };

        $this->label = Yii::t('app','Czas pracy');
        $this->value = function ($item, $key, $index, $column)
        {
            $workingTime = $item->getWorkingTime($this->parentModel->id,false, true);
            return $workingTime;
        };

        parent::init();
    }
}