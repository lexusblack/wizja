<?php
namespace common\components;

use yii\grid\ActionColumn as BaseActionColumn;
use yii\helpers\Url;
use yii\bootstrap\Html;

class ActionColumn extends BaseActionColumn
{
    public $controllerId;

    public function init()
    {
        $this->header = false;
        if ($this->controllerId != null)
        {
            $this->urlCreator = function($action, $model, $key, $index)
            {
                $params = is_array($key) ? $key : ['id' => (string) $key];
                $params[0] = $this->controllerId.'/' . $action;

                return Url::toRoute($params);
            };
        }
        parent::init();
    }

    public function renderPageSummaryCell()
    {
        return Html::tag('td', '&nbsp;');
    }
}