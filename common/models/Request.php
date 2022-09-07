<?php

namespace common\models;

use Yii;
use \common\models\base\Request as BaseRequest;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "request".
 */
class Request extends BaseRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'company_id', 'mail', 'status'], 'required'],
            [['status', 'event_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'mail', 'username'], 'string', 'max' => 255],
            [['company_id'], 'string', 'max' => 20]
        ]);
    }

    public function getTypeList()
    {
        return [1=>Yii::t('app', 'Błąd'), 2=>Yii::t('app','Pytanie'), 3=>Yii::t('app','Nowa funkcjonalność')];
    } 

    public function getPriorityList()
    {
        return [1=>Yii::t('app', 'Niski'), 2=>Yii::t('app','Wysoki'), 3=>Yii::t('app','Uniemożliwiający pracę')];
    }

    public function getStatusList()
    {
        return [1=>Yii::t('app', 'Oczekujące'), 2=>Yii::t('app','W realizacji'), 3=>Yii::t('app','Ukończone'), 4=>Yii::t('app','Odrzucone')];
    }


    public function getNotRead()
    {
        if (Yii::$app->params['companyID']=="admin")
        {
            $read = ArrayHelper::map(RequestRead::find()->where(['type'=>2])->asArray()->all(), 'request_id', 'request_id');
            $number = Request::find()->where(['NOT IN', 'id', $read])->count();
        }else{
            $read = ArrayHelper::map(RequestRead::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'request_id', 'request_id');
            $number = Request::find()->where(['NOT IN', 'id', $read])->andWhere(['company_id'=>Yii::$app->params['companyID']])->andWhere(['mail'=>Yii::$app->user->identity->email])->count();
        }
        
        return $number;
    }

    public function addRead()
    {
        if (!$this->isRead())
        {
        $read = new RequestRead();
        $read->request_id = $this->id;
        if (Yii::$app->params['companyID']=="admin")
        {
            $read->type = 2;
        }else{
            $read->type = 1;
            $read->user_id = Yii::$app->user->id;
        }
        $read->save();
        }
    }

    public function isRead()
    {
        if (Yii::$app->params['companyID']!="admin")
        {
            $read = RequestRead::find()->where(['request_id'=>$this->id, 'user_id'=>Yii::$app->user->id])->count();
        }else{
            $read = RequestRead::find()->where(['request_id'=>$this->id, 'type'=>2])->count();
        }
        return $read;
    }

    public function removeRead()
    {
        if (Yii::$app->params['companyID']=="admin")
        {
            RequestRead::deleteAll(['request_id'=>$this->id, 'type'=>1]);
        }else{
            RequestRead::deleteAll(['request_id'=>$this->id, 'type'=>2]);
        }
    }

    public function getHistory()
    {
        return RequestHistory::find()->where(['request_id'=>$this->id])->all();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert)
        {
            $this->addRead();
        }else{
            if ($this->status!=$changedAttributes['status'])
            {
                $rh = new RequestHistory();
                $rh->request_id = $this->id;
                $rh->user_id = Yii::$app->user->id;
                $rh->datetime = date('Y-m-d H:i:s');
                $rh->status = $this->status;
                $rh->save();
            }
        }
    }

    
	
}
