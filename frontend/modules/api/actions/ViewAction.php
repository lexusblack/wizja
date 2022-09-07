<?php
namespace frontend\modules\api\actions;

use yii\helpers\StringHelper;
use yii\helpers\Inflector;

use yii\rest\ViewAction as BaseAction;

class ViewAction extends BaseAction
{

    public function run($id)
    {
        $model = parent::run($id);

        $modelClass = StringHelper::basename($this->controller->modelClass);
        $envelope = Inflector::camel2id($modelClass);

//        $model->refresh();
//        var_dump($envelope);

        return [$envelope=>$model];

    }

}