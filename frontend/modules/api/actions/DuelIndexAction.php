<?php
namespace app\modules\api\actions;

use yii\data\ActiveDataProvider;

use app\modules\api\actions\IndexAction as BaseIndexAction;
use yii\web\Response;
use Yii;

class DuelIndexAction extends BaseIndexAction
{
    protected function loadParams()
    {
        $request = Yii::$app->request;
        $this->date = $request->get('date', null);;
        $this->objectUUID = $request->get('duelUUIDs', null);
    }

}