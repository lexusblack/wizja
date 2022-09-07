<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "rent_outer_gear".
 *
 * @property integer $rent_id
 * @property integer $outer_gear_id
 * @property integer $quantity
 * @property integer $discount
 * @property string $start_time
 * @property string $end_time
 * @property integer $type
 * @property integer $planned
 * @property integer $order_id
 * @property integer $confirm
 * @property string $return_time
 * @property string $reception_time
 * @property double $price
 * @property string $update_time
 * @property string $created_at
 * @property integer $user_id
 * @property integer $description
 *
 * @property \common\models\Rent $rent
 * @property \common\models\OuterGear $outerGear
 */
class RentOuterGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'rent',
            'outerGear'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_id', 'outer_gear_id'], 'required'],
            [['rent_id', 'outer_gear_id', 'quantity', 'discount', 'type', 'planned', 'order_id', 'confirm', 'user_id', 'description'], 'integer'],
            [['start_time', 'end_time', 'return_time', 'reception_time', 'update_time', 'created_at'], 'safe'],
            [['price'], 'number'],
            [['rent_id', 'outer_gear_id'], 'unique', 'targetAttribute' => ['rent_id', 'outer_gear_id'], 'message' => 'The combination of Rent ID and Outer Gear ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_outer_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rent_id' => 'Rent ID',
            'outer_gear_id' => 'Outer Gear ID',
            'quantity' => 'Quantity',
            'discount' => 'Discount',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'type' => 'Type',
            'planned' => 'Planned',
            'order_id' => 'Order ID',
            'confirm' => 'Confirm',
            'return_time' => 'Return Time',
            'reception_time' => 'Reception Time',
            'price' => 'Price',
            'update_time' => 'Update Time',
            'user_id' => 'User ID',
            'description' => 'Description',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGear()
    {
        return $this->hasOne(\common\models\OuterGear::className(), ['id' => 'outer_gear_id']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
