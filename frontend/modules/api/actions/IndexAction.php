<?php
namespace app\modules\api\actions;

use yii\data\ActiveDataProvider;

use yii\rest\IndexAction as BaseIndexAction;
use yii\web\Response;
use Yii;

class IndexAction extends BaseIndexAction
{
    protected $date;
    protected $duelUUID;
    protected $objectUUID;

    public function run($date=null)
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $this->loadParams();

        return $this->prepareDataProvider();

    }

    protected function prepareDataProvider()
    {
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }

        $modelClass = $this->modelClass;
        $query =  $modelClass::find();

        $query->andFilterWhere(['>', 'update_time', $this->date]);

        $query->andFilterWhere(['duelUUID'=>$this->duelUUID]);
        $query->andFilterWhere(['objectUUID'=>$this->objectUUID]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' =>
                false,
//                [
//                'pageSize' => false,
//            ]
        ]);
    }

    protected function loadParams()
    {
        $request = Yii::$app->request;
        $this->date = $request->get('date', null);;
        $this->duelUUID = $request->get('duelUUIDs', null);
        $this->objectUUID = $request->get('objectUUID', null);
    }
}