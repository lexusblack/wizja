<?php

namespace common\models;

use Yii;
use \common\models\base\GearSet as BaseGearSet;

/**
 * This is the model class for table "gear_set".
 */
class GearSet extends BaseGearSet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['category_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'photo'], 'string', 'max' => 255]
        ]);
    }

    public function getPhotoUrl()
    {
        if ($this->photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/gear/'.$this->photo);
        }

    }

    public function saveOuterGear($outers)
    {
        GearSetOuterItem::deleteAll(['gear_set_id'=>$this->id]);
        if ($outers)
        {
            foreach ($outers as $o)
            {
                $outer = new GearSetOuterItem;
                $outer->gear_set_id = $this->id;
                $outer->outer_gear_model_id = $o['outer_gear_model_id'];
                $outer->quantity = $o['quantity'];
                $outer->save();
            }
        }

    }
	
}
