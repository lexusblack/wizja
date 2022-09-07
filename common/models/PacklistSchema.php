<?php

namespace common\models;

use Yii;
use \common\models\base\PacklistSchema as BasePacklistSchema;

/**
 * This is the model class for table "packlist_schema".
 */
class PacklistSchema extends BasePacklistSchema
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string', 'max' => 45]
        ]);
    }
	
}
