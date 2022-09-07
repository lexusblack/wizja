<?php
namespace frontend\modules\api\controllers;

use app\modules\api\actions\DuelIndexAction;
use frontend\modules\api\components\BaseController;

class DuelController extends BaseController
{
    public $modelClass = 'common\models\Duel';

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['class'] = DuelIndexAction::className();

        return $actions;
    }

}