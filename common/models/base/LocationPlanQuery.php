<?php

namespace common\models\base;

use yii\db\ActiveQuery;

class LocationPlanQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        $this->andOnCondition(['or', ['public' => 1], ['owner'=>\Yii::$app->params['companyID']]]);
        parent::init();
    }
}
?>