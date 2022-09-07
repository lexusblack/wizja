<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\query\GearCompany]].
 *
 * @see \common\models\query\GearCompany
 */
class GearCompanyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \common\models\query\GearCompany[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GearCompany|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}