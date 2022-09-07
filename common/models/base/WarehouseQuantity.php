<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "warehouse_quantity".
 *
 * @property integer $gear_id
 * @property integer $warehouse_id
 * @property integer $quantity
 */
class WarehouseQuantity extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id'], 'required'],
            [['gear_id', 'warehouse_id', 'quantity'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehouse_quantity';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gear_id' => 'Gear ID',
            'warehouse_id' => 'Warehouse ID',
            'quantity' => 'Quantity',
        ];
    }
}
