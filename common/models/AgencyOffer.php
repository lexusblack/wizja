<?php

namespace common\models;

use Yii;
use \common\models\base\AgencyOffer as BaseAgencyOffer;

/**
 * This is the model class for table "agency_offer".
 */
class AgencyOffer extends BaseAgencyOffer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['customer_id', 'contact_id', 'manager_id', 'location_id', 'event_id', 'schema_id'], 'integer'],
            [['event_start', 'event_end', 'offer_date', 'payment_date', 'create_time', 'update_time', 'provision'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public function createServices()
    {
        $categories = ServiceCategory::find()->where(['in_offer'=>1])->andWhere(['schema_id'=>$this->schema_id])->all();
        foreach ($categories as $c)
        {
            $m = new AgencyOfferServiceCategory;
            $m->name = $c->name;
            $m->position = $c->position;
            $m->agency_offer_id = $this->id;
            $m->provizion = 1;
            $m->color = $c->color;
            $m->save();
            foreach ($c->services as $s)
            {
                $o = new AgencyOfferService;
                $o->name = $s->name;
                $o->position = $s->position;
                $o->agency_offer_id = $this->id;
                $o->category_id = $m->id;
                $o->save();
            }
        }
    }

    public function getServiceData()
    {
        $models = AgencyOfferServiceCategory::find()->where(['agency_offer_id'=>$this->id])->orderBy(['position'=>SORT_ASC])->all();
        $data = [];
        foreach ($models as $model)
        {
            $obj['id'] = $model->id;
            $obj['name'] = $model->name;
            $obj['positions'] = $model->position;
            $obj['items'] = [];
            $obj['provizion'] = $model->provizion;
            $obj['color'] = $model->color;
            foreach ($model->agencyOfferServices as $service)
            {
                $sdata['id'] = $service->id;
                $sdata['name'] = $service->name;
                $sdata['position'] = $service->position;
                $sdata['price'] = $service->price;
                $sdata['client_price'] = $service->client_price;
                $sdata['total_price'] = $service->total_price;
                $sdata['total_profit'] = $service->total_profit;
                $sdata['count'] = $service->count;
                $sdata['info'] = $service->info;
                $obj['items'][] = $sdata;
            }
            $data[] = $obj;
        }
        return $data;
    }

    public function getServiceValue()
    {
        $models = AgencyOfferService::find()->where(['agency_offer_id'=>$this->id])->all();
        $sum =0;
        foreach ($models as $m)
        {
            $sum+=$m->total_price;
        }
        return $sum;
    }

    public function getServiceCategoryPosition()
    {
        $models = AgencyOfferServiceCategory::find()->where(['agency_offer_id'=>$this->id])->orderBy(['position'=>SORT_DESC])->one();
        if (!$models)
            return 1;
        else
            return $models->position+1;
    }

    public function getNettoValue()
    {
        return $this->getServiceValue();
    }
    public function getProfitValue()
    {
        $models = AgencyOfferService::find()->where(['agency_offer_id'=>$this->id])->all();
        $sum =0;
        foreach ($models as $m)
        {
            $sum+=$m->total_profit;
        }
        $cats = AgencyOfferServiceCategory::find()->where(['agency_offer_id'=>$this->id, 'provizion'=>1])->all();
        $provision = 0;
        foreach ($cats as $cat)
        {
            foreach ($cat->agencyOfferServices as $m)
            {
                $provision+=$m->total_price;
            }
        }
        $provision = $provision*$this->provision/100;
        return $sum+$provision;
    }
}
