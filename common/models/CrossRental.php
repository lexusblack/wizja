<?php

namespace common\models;

use \common\models\base\CrossRental as BaseCrossRental;

/**
 * This is the model class for table "cross_rental".
 */
class CrossRental extends BaseCrossRental
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_model_id', 'owner_gear_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['owner', 'owner_city'], 'string', 'max' => 45],
            [['owner_name'], 'string', 'max' => 255]
        ]);
    }
	
public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
            EnNote::createNote('CRN', $this);


    }
}
