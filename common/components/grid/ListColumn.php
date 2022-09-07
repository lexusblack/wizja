<?php
namespace common\components\grid;

use yii\grid\DataColumn as BaseColumn;
use yii\bootstrap\Html;

class ListColumn extends BaseColumn
{
    public $asIcon = true;
    public $filter = [
        0 => 'Nie',
        1 => 'Tak'
    ];

    public $icons = [
        0 => 'remove',
        1 => 'ok'
    ];

    public function getDataCellValue($model, $key, $index)
    {
        $list = $this->filter;
        $index = $model->{$this->attribute};
        if (isset($list[$index]))
        {
            if ($this->asIcon)
            {
                $value = Html::icon($this->icons[$index]);
                $this->format = 'html';
            }
            else
            {
                $value = $list[$index];
            }
        }
        else
        {
            $value = UNDEFINDED_STRING;
        }

        return $value;
    }
}
