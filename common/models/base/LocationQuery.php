<?php

namespace common\models\base;

use yii\db\ActiveQuery;

class LocationQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        $this->andOnCondition(['or', ['public' => 2], ['owner'=>\Yii::$app->params['companyID']]]);
        parent::init();
    }

    public function editable()
    {
        return $this->andOnCondition([ 'IN', 'public', [0,1]]);
    }
}
?>