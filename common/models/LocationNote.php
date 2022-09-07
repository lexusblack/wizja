<?php

namespace common\models;

use \common\models\base\LocationNote as BaseLocationNote;

/**
 * This is the model class for table "location_note".
 */
class LocationNote extends BaseLocationNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['text'], 'string'],
            [['created_by', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ]);
    }
	
}
