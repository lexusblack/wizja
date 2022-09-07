<?php

namespace common\models;

use Yii;
use \common\models\base\AgencyOfferServiceCategory as BaseAgencyOfferServiceCategory;

/**
 * This is the model class for table "agency_offer_service_category".
 */
class AgencyOfferServiceCategory extends BaseAgencyOfferServiceCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['agency_offer_id', 'position', 'provizion'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'color'], 'string', 'max' => 255]
        ]);
    }
	
}
