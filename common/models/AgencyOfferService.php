<?php

namespace common\models;

use Yii;
use \common\models\base\AgencyOfferService as BaseAgencyOfferService;

/**
 * This is the model class for table "agency_offer_service".
 */
class AgencyOfferService extends BaseAgencyOfferService
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['agency_offer_id', 'position', 'count', 'category_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['price', 'client_price', 'total_price'], 'number'],
            [['info'], 'string'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
