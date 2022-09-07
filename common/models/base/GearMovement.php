<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_movement".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $gear_id
 * @property integer $user_id
 * @property integer $quantity
 * @property string $datetime
 * @property integer $warehouse_from
 * @property integer $warehouse_to
 *
 * @property \common\models\Warehouse $warehouseFrom
 * @property \common\models\Gear $gear
 * @property \common\models\Warehouse $warehouseTo
 * @property \common\models\User $user
 */
class GearMovement extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'warehouseFrom',
            'gear',
            'warehouseTo',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'gear_id', 'user_id', 'quantity', 'datetime'], 'required'],
            [['type', 'gear_id', 'user_id', 'quantity', 'warehouse_from', 'warehouse_to'], 'integer'],
            [['datetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_movement';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'gear_id' => 'Gear ID',
            'user_id' => 'User ID',
            'quantity' => 'Quantity',
            'datetime' => 'Datetime',
            'warehouse_from' => 'Warehouse From',
            'warehouse_to' => 'Warehouse To',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouseFrom()
    {
        return $this->hasOne(\common\models\Warehouse::className(), ['id' => 'warehouse_from']);
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
    public function getWarehouseTo()
    {
        return $this->hasOne(\common\models\Warehouse::className(), ['id' => 'warehouse_to']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
