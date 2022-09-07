<?php

namespace common\models;

use Yii;
use \common\models\base\OfferSchedule as BaseOfferSchedule;

/**
 * This is the model class for table "offer_schedule".
 */
class OfferSchedule extends BaseOfferSchedule
{
    /**
     * @inheritdoc
     */

    public $dateRange;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['start_time', 'end_time', 'color'], 'safe'],
            [['name', 'prefix'], 'string', 'max' => 45]
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $offer = Offer::findOne($this->offer_id);
        $offer->updateSchedule();

    }

    public function beforeDelete()
    {
        OfferVehicle::deleteAll(['type'=>$this->id]);
        OfferRole::deleteAll(['time_type'=>$this->id]);
       \backend\modules\offers\models\OfferExtraItem::deleteAll(['time_type'=>$this->id]);
        return true;
    }

    public function getPeriodTime()
    {
        $difference = ceil(abs(strtotime($this->end_time) - strtotime($this->start_time)) / 3600);
            return $difference;
    }
	
}
