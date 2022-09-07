<?php


namespace backend\actions;


use pheme\settings\SettingsAction;
use Yii;

class SettingAction extends SettingsAction {

    public function run() {
        $this->controller->layout = '@backend/themes/e4e/layouts/panel';
        Yii::$app->view->params['active_tab'] = 2;

       return  parent::run();
    }
}