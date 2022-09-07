<?php

namespace common\models;

use Yii;
use \common\models\base\Firm as BaseFirm;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "firm".
 */
class Firm extends BaseFirm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'required'],
            [['active'], 'integer'],
            [['name', 'address', 'city', 'logo', 'email', 'warehouse_adress', 'warehouse_city'], 'string', 'max' => 255],
            [['zip', 'nip', 'phone', 'bank_name', 'bank_number', 'warehouse_zip'], 'string', 'max' => 45]
        ]);
    }

    public function getList()
    {
        return ArrayHelper::map(Firm::find()->asArray()->all(), 'id', 'name');
    }
	
}
