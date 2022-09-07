<?php
namespace common\components;
use Yii;

trait SettingsTrait
{
    public function loadValues()
    {
        foreach ($this->attributes() as $key) {
            $this->{$key} = Yii::$app->settings->get($key, $this->formName());
        }
    }

    public function saveValues($params=null)
    {
        if ($params==null)
        {
            $params = $this->toArray();
        }

        foreach ($params as $key => $value) {
            \Yii::$app->settings->set($key, $value, $this->formName());
        }

        return true;
    }
}