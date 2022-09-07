<?php

namespace common\models;
use yii\data\ActiveDataProvider;
use Yii;
use \common\models\base\Contact as BaseContact;

/**
 * This is the model class for table "contact".
 */
class Contact extends BaseContact
{
    public function rules()
    {
        $rules = [
            ['email', 'email'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public static function getList($customerId=null, $term=null)
    {
        $models = static::find()
            ->andFilterWhere(['status'=>1])
            ->andFilterWhere(['customer_id'=>$customerId])
            ->andFilterWhere([ 'or',
                ['like', 'first_name', $term],
                ['like', 'last_name', $term]

            ])
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->getDisplayLabel();
        }

        return $list;
    }

    public function getDisplayLabel()
    {
        $attributes = [
            $this->last_name,
            $this->first_name,
            //$this->phone,
            //$this->email,
        ];
        $attributes = array_filter($attributes);
        return $this->first_name." ".$this->last_name;
    }

    public function getAssignedMeetings()
    {
        $query = $this->getMeetings();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=> ['defaultOrder' => ['start_time'=>SORT_DESC]],
            'pagination'=>false,
        ]);

        return $dataProvider;       
    }
    public function getAssignedEvents()
    {
        $query = $this->getEvents();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]],
            'pagination'=>false,
        ]);

        return $dataProvider;       
    }

    public function getPhotoUrl()
    {
        return Yii::getAlias('@uploads/contact/'.$this->photo);
    }

    public function beforeDelete()
    {
        $this->customer->createLog('contact_delete', $this->id);
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {
                Note::createNote(4, 'contactAdded', $this, $this->customer_id);
                $customer = Customer::findOne($this->customer_id);     
                $customer->createLog('contact_create', $this->id);   
                
            }
         
    }
}
