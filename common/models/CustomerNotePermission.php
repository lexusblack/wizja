<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerNotePermission as BaseCustomerNotePermission;

/**
 * This is the model class for table "customer_note_permission".
 */
class CustomerNotePermission extends BaseCustomerNotePermission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['customer_note_id'], 'integer'],
            [['permission'], 'string', 'max' => 255]
        ]);
    }
	
}
