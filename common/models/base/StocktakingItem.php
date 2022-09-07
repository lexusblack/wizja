<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "stocktaking_item".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $gear_item_id
 * @property integer $stocktaking_id
 * @property string $datetime
 * @property integer $quantity
 *
 * @property \common\models\GearItem $gearItem
 * @property \common\models\Stocktaking $stocktaking
 * @property \common\models\User $user
 */
class StocktakingItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gearItem',
            'stocktaking',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'gear_item_id', 'stocktaking_id', 'quantity'], 'integer'],
            [['datetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stocktaking_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'gear_item_id' => 'Gear Item ID',
            'stocktaking_id' => 'Stocktaking ID',
            'datetime' => 'Datetime',
            'quantity' => 'Quantity',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItem()
    {
        return $this->hasOne(\common\models\GearItem::className(), ['id' => 'gear_item_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocktaking()
    {
        return $this->hasOne(\common\models\Stocktaking::className(), ['id' => 'stocktaking_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
