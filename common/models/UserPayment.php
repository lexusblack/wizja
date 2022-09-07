<?php

namespace common\models;

use Yii;
use \common\models\base\UserPayment as BaseUserPayment;

/**
 * This is the model class for table "user_payment".
 */
class UserPayment extends BaseUserPayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'month', 'year', 'creator_id'], 'integer'],
            [['amount'], 'number'],
            [['datetime', 'payment_method'], 'safe'],
            [['description'], 'string', 'max' => 255]
        ]);
    }

    public function getMonthAmount($year, $month)
    {
        $payments = UserPayment::find()->where(['year'=>$year, 'month'=>$month])->asArray()->all();
        $sum = 0;
        foreach ($payments as $p)
        {
            $sum +=$p['amount'];
        }
        return $sum;
    }

    public function getMonthAmountByTypes($year, $month)
    {
        $payments = UserPayment::find()->where(['year'=>$year, 'month'=>$month])->asArray()->all();
        $sum = [];
        $total = 0;
        foreach ($payments as $p)
        {
            if (!isset($sum[$p['payment_method']]))
            {
                $sum[$p['payment_method']] = 0;
            }
            $sum[$p['payment_method']] +=$p['amount'];
        }
        $return = "";
        foreach ($sum as $k=>$v)
        {
            $return .= "<br/>".$k.": ".Yii::$app->formatter->asCurrency($v);
        }
        return $return;
    }
	
}
