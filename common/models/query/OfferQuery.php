<?php
namespace common\models\query;

use common\models\Offer;
use yii\db\ActiveQuery;

class OfferQuery extends ActiveQuery
{
    public function accepted($status=Offer::STATUS_ACCEPT)
    {

        $query = $this->andWhere(['status'=>$status]);
        return $query;
    }

}