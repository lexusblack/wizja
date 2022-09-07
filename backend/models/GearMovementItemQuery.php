<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[\app\models\GearMovementItem]].
 *
 * @see \app\models\GearMovementItem
 */
class GearMovementItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\GearMovementItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\GearMovementItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
