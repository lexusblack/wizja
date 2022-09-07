<?php

namespace common\models;

use Yii;
use \common\models\base\CompanyLog as BaseCompanyLog;

/**
 * This is the model class for table "company_log".
 */
class CompanyLog extends BaseCompanyLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['datetime'], 'safe'],
            [['users', 'rents', 'events', 'gears'], 'integer'],
            [['company_id'], 'string', 'max' => 45]
        ]);
    }
	
}
