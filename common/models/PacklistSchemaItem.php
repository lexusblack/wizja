<?php

namespace common\models;

use Yii;
use \common\models\base\PacklistSchemaItem as BasePacklistSchemaItem;

/**
 * This is the model class for table "packlist_schema_item".
 */
class PacklistSchemaItem extends BasePacklistSchemaItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['packlist_schema_id'], 'integer'],
            [['name', 'color'], 'string', 'max' => 45]
        ]);
    }
	
}
