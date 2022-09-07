<?php

namespace common\models;

use \common\models\base\LocationPlan as BaseLocationPlan;

/**
 * This is the model class for table "location_plan".
 */
class LocationPlan extends BaseLocationPlan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['status', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }

    public function getName()
    {
        if ($this->name!="")
            return $this->name;
        else
            return $this->filename;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (($this->public==1)&&($this->status==1))
            EnNote::createNote('Plan', $this);


    }
	
}
