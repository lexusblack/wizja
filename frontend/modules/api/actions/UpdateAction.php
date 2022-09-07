<?php
namespace frontend\modules\api\actions;

use yii\helpers\StringHelper;
use yii\helpers\Inflector;

use yii\rest\UpdateAction as BaseAction;

class UpdateAction extends BaseAction
{

    public function run($id)
    {
        $model = parent::run($id);

        $modelClass = StringHelper::basename($this->controller->modelClass);
        $envelope = Inflector::camel2id($modelClass);

        $model->refresh();

        return [$envelope=>$model];

    }

}