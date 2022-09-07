<?php
namespace frontend\modules\api\actions;

use yii\helpers\StringHelper;
use yii\helpers\Inflector;

use yii\rest\CreateAction as BaseAction;

class CreateAction extends BaseAction
{

    public function run($date=null)
    {
        $model = parent::run();

        $modelClass = StringHelper::basename($this->controller->modelClass);
        $envelope = Inflector::camel2id($modelClass);

        $model->refresh();

        if ($envelope=='question')
        {
            $model->sendToApproveMail();
        }

        return [$envelope=>$model];

    }

}