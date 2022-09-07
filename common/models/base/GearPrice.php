<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_price".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $price_group_id
 * @property string $price
 *
 * @property \common\models\Gear $gear
 * @property \common\models\PriceGroup $priceGroup
 */
class GearPrice extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gear',
            'priceGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'gears_price_id'], 'required'],
            [['gear_id', 'gears_price_id', 'add_to_event'], 'integer'],
            [['price', 'cost'], 'number'],
            [['cost_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => 'Gear ID',
            'gears_price_id' => 'Price Group ID',
            'price' => 'Price',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearsPrice()
    {
        return $this->hasOne(\common\models\GearsPrice::className(), ['id' => 'gears_price_id']);
    }
    }
