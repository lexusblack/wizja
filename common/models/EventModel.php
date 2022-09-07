<?php

namespace common\models;

use Yii;
use \common\models\base\EventModel as BaseEventModel;

/**
 * This is the model class for table "event_model".
 */
class EventModel extends BaseEventModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active', 'type', 'schedule_type_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public static function getOfferTypes(){
        $return = \common\helpers\ArrayHelper::map(EventModel::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
        $return[1000000] = Yii::t('app', 'Wypożyczenie');
        return $return;
        
    }
	
}
