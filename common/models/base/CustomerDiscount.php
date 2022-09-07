<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "customer_discount".
 *
 * @property integer $id
 * @property integer $discount
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \common\models\CustomerDiscountCategory[] $customerDiscountCategories
 * @property \common\models\GearCategory[] $categories
 * @property \common\models\CustomerDiscountCustomer[] $customerDiscountCustomers
 * @property \common\models\Customer[] $customers
 * @property string $aliasModel
 */
abstract class CustomerDiscount extends \common\components\BaseActiveRecord
{

    public $category_ids;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_discount';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount'], 'required'],
            [['discount'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Stworzono'),
            'updated_at' => Yii::t('app', 'Zaktualizowano'),
            'discount' => Yii::t('app', 'Rabat'),
            'category_ids' => Yii::t('app', 'Kategorie')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscountCategories()
    {
        return $this->hasMany(\common\models\CustomerDiscountCategory::className(), ['customer_discount_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(\common\models\GearCategory::className(), ['id' => 'category_id'])->viaTable('customer_discount_category', ['customer_discount_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscountCustomers()
    {
        return $this->hasMany(\common\models\CustomerDiscountCustomer::className(), ['customer_discount_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(\common\models\Customer::className(), ['id' => 'customer_id'])->viaTable('customer_discount_customer', ['customer_discount_id' => 'id']);
    }




}
