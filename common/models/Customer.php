<?php

namespace common\models;
use yii\data\ActiveDataProvider;
use Yii;
use \common\models\base\Customer as BaseCustomer;
use common\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "customer".
 */
class Customer extends BaseCustomer
{

    public function rules()
    {
        $rules = [
            [['name'], 'required'],
            ['email', 'email'],
            [['nip'], 'unique']
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function getAppList()
    {
        $customers = Customer::find()->where(['active'=>1])->all();
        return $customers;
    }

    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getCustomerAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public static function getList($term=null, $attrs=[])
    {
        /* @var $models static[] */
        $models = static::find()
            ->andFilterWhere([ 'or',
                ['like', 'name', $term],
                ['like', 'company', $term]

            ])
            ->orFilterWhere(['like', 'nip', $term])
            ->andFilterWhere(['active'=> 1])
            ->andFilterWhere($attrs)
            ->orderBy(['name'=>SORT_ASC])
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->getDisplayLabel();
            if (empty($model->nip) == false)
            {
                $list[$model->id] .= ' ['.$model->nip.']';
            }
        }

        return $list;
    }

    public static function getPaymentArray()
    {
        /* @var $models static[] */
        $models = static::find()
            ->andFilterWhere(['active'=> 1])
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->payment_days;
        }

        return $list;
    }

    public function getDisplayLabel()
    {
        $attributes = [
            $this->name,
//            $this->company,
            $this->city,
        ];
        $attributes = array_filter($attributes);
        return implode(', ', $attributes);
    }

    public function getLogoUrl()
    {
        return Yii::getAlias('@uploads/logo/'.$this->logo);
    }

    public function getAssignedContacts($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getContacts();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getAssignedLogs($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getCustomerLogs();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getAssignedMeetings($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getMeetings()->orderBy(['start_time'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedEvents()
    {
        $query = $this->getEvents();

        $query->orderBy = ['event_end'=>SORT_DESC, 'event_start'=>SORT_DESC];

        $models = $query->all();

        return $models;
    }

    public function getAssignedEvents2()
    {
        $query = $this->getEvents();

        $query->orderBy = ['event_end'=>SORT_DESC, 'event_start'=>SORT_DESC];

        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedRents2()
    {
        $query = $this->getRents();

        $query->orderBy = ['end_time'=>SORT_DESC, 'start_time'=>SORT_DESC];

        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedAgencyOffers()
    {
        $query = $this->getAgencyOffers();

        $query->orderBy = ['event_end'=>SORT_DESC, 'event_start'=>SORT_DESC];
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOffers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getOffers();
        $query->orderBy = ['event_end'=>SORT_DESC, 'event_start'=>SORT_DESC];
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
        ]);

        return $dataProvider;
    }


    public function getAssignedRents()
    {
        $query = $this->getRents();

        $query->orderBy = ['end_time'=>SORT_DESC, 'start_time'=>SORT_DESC];

        $models = $query->all();

        return $models;
    }

    public function getDiscountsList()
    {
        $list = [];
        foreach ($this->customerDiscounts as $model)
        {
            foreach ($model->customerDiscountCategories as $discountCategory)
            {
                $list[$discountCategory->category_id] = $model->discount;
            }
        }



        return $list;
    }

    public function attributeLabels()
    {
        $labels = [
            'name'=>Yii::t('app', 'Nazwa firmy'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getPlaceholderMap()
    {
        $formatter = Yii::$app->formatter;
        $map = [
            'name' => $this->name,
            'tel' => $this->phone,
            'mail' => $this->email,
        ];

        return $map;
    }

    public function createLog($type, $id)
    {
        $log = new CustomerLog();
        $log->customer_id = $this->id;
        $log->user_id =  Yii::$app->user->identity->id;
        if ($type=='offer_create')
        {
            $offer = Offer::findOne($id);
            $content = Yii::t('app', 'Dodano ofertę ').$offer->name." ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$offer->id]);
            $log->content = $content;
        }
        if ($type=='offer_delete')
        {
            $offer = Offer::findOne($id);
            $content = Yii::t('app', 'Usunięto ofertę ').$offer->name;
            $log->content = $content;
        }
        if ($type=='offer_update')
        {
            $offer = Offer::findOne($id);
            $content = Yii::t('app', 'Zmieniono status oferty ').$offer->name." na ".Offer::getStatusList()[$offer->status]." ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$offer->id]);
            $log->content = $content;
        }
        if ($type=='note_create')
        {
            $note = CustomerNote::findOne($id);
            $content = Yii::t('app', 'Dodano notatkę: ').$note->name;
            $log->content = $content;
        }
        if ($type=='file_create')
        {
            $note = CustomerAttachment::findOne($id);
            $content = Yii::t('app', 'Dodano załącznik: ').$note->filename." ".Html::a(Yii::t('app', 'Zobacz'), ['/customer-attachment/download', 'id'=>$note->id]);;
            $log->content = $content;
        }
        if ($type=='file_delete')
        {
            $note = CustomerAttachment::findOne($id);
            $content = Yii::t('app', 'Usunięto załącznik: ').$note->filename;
            $log->content = $content;
        }
        if ($type=='contact_create')
        {
            $contact = Contact::findOne($id);
            $content = Yii::t('app', 'Dodano kontakt ').$contact->displayLabel." ".Html::a(Yii::t('app', 'Zobacz'), ['/contact/view', 'id'=>$contact->id]);
            $log->content = $content;
        }
        if ($type=='contact_delete')
        {
            $contact = Contact::findOne($id);
            $content = Yii::t('app', 'Usunięto kontakt ').$contact->displayLabel;
            $log->content = $content;
        }
        if ($type=='event_create')
        {
            $event = Event::findOne($id);
            $content = Yii::t('app', 'Dodano wydarzenie ').$event->name." ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$event->id]);
            $log->content = $content;
        }
        if ($type=='rent_create')
        {
            $rent = Rent::findOne($id);
            $content = Yii::t('app', 'Dodano wypożyczenie ').$rent->name." ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$rent->id]);
            $log->content = $content;
        }
        $log->save();

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(4, 'customerAdded', $this, $this->id);
            }else{
                if (((isset($changedAttributes['name']))&&($this->name!=$changedAttributes['name']))||((isset($changedAttributes['address']))&&($this->address!=$changedAttributes['address'])))
                {
                     if (isset($changedAttributes['name']))
                     {
                        $name = $changedAttributes['name'];
                     }else{
                        $name = $this->name;
                     }
                    if (isset($changedAttributes['address']))
                     {
                        $address = $changedAttributes['address'];
                     }else{
                        $address = $this->address;
                     }
                    if (isset($changedAttributes['zip']))
                     {
                        $zip = $changedAttributes['zip'];
                     }else{
                        $zip = $this->zip;
                     }
                    if (isset($changedAttributes['city']))
                     {
                        $city = $changedAttributes['city'];
                     }else{
                        $city = $this->city;
                     }
                     $log = new CustomerLog();
                        $log->customer_id = $this->id;
                         $log->user_id =  Yii::$app->user->identity->id;
                         $content = Yii::t('app', 'Zmieniono dane podstawowe kontrahenta. ').Yii::t('app', 'Poprzednie dane: ').$name.", ".$address.", ".$zip." ".$city;
                        $log->content = $content;
                        $log->save();
                }
            }
         
    }

    public function fields()
    {
        $fields = parent::fields();

        if ($this->isNewRecord) {
            return $fields;
        }
        $fields['contacts'] = function() {
            $contacts = [];
            foreach ($this->contacts as $contact)
            {
                $contacts[] = $contact;
            }
            if (!$contacts)
                $contacts = (object)[];
            return $contacts;
        };
        return $fields;
    }

    public function isInGroup($type)
    {
        foreach ($this->customerTypes as $t)
        {
            if ($t->id == $type)
                return true;
        }
        return false;
    }
}
