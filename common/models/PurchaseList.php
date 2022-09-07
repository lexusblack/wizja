<?php

namespace common\models;

use Yii;
use \common\models\base\PurchaseList as BasePurchaseList;
use \common\helpers\ArrayHelper;
/**
 * This is the model class for table "purchase_list".
 */
class PurchaseList extends BasePurchaseList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['datetime'], 'safe'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ]);
    }

    public function getStatusList()
    {
        return [0=>Yii::t('app', 'Nowa'), 1=>Yii::t('app', 'Zrealizowana')];
    }

    public static function getNoCompanyLabel()
    {
        $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 days" ) );
        $myDate2 = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+ 10 days" ) );
        $eventIds = ArrayHelper::map(Event::find()->where(['>', 'event_end', $myDate])->andWhere(['<', 'event_start', $myDate2])->asArray()->all(), 'id', 'id');
        $number = EventOuterGearModel::find()->where(['resolved'=>0])->andWhere(['in', 'event_id', $eventIds])->andWhere(['prod'=>1])->count();
        return '<span class="badge badge-warning pull-right">'.$number.'</span>';
    }

    public static function getCompanyLabel()
    {
        $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 days" ) );
        $myDate2 = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+ 10 days" ) );
        $eventIds = ArrayHelper::map(Event::find()->where(['>', 'event_end', $myDate])->andWhere(['<', 'event_start', $myDate2])->asArray()->all(), 'id', 'id');
        $number = EventOuterGear::find()->where(['order_id'=>null])->andWhere(['in', 'event_id', $eventIds])->andWhere(['prod'=>1])->count();
        return '<span class="badge badge-primary pull-right">'.$number.'</span>';
    }
	
}
