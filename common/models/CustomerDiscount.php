<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\CustomerDiscount as BaseCustomerDiscount;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "customer_discount".
 */
class CustomerDiscount extends BaseCustomerDiscount
{
    public $category_ids;

    public function behaviors()
    {
        $behaviors =  [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                    // ActiveRecord::EVENT_BEFORE_INSERT => 'update_time',
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() { return date("Y-m-d H:i:s"); },
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function rules()
    {
        $rules = [
            [['discount'], 'integer', 'max' => 100],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [
            'discount' => Yii::t('app', 'Rabat %'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
//        return [
//            'id' => 'ID',
//            'gear_cat_id' => 'Kategorja',
//            'customer_id' => 'Klient',
//            'created_at' => 'Created At',
//            'updated_at' => 'Updated At',
//
//        ];
    }

    public function getCategoriesLabel($separator='; ')
    {
        $models = $this->categories;
        $list = ArrayHelper::map($models, 'id', 'name');
        return implode($separator, $list);
    }

    public function getCustomersLabel($separator='; ')
    {
        $models = $this->customers;
        $list = ArrayHelper::map($models, 'id', 'name');
        return implode($separator, $list);
    }
}
