<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_movement_item".
 *
 * @property integer $id
 * @property integer $gear_item_id
 * @property integer $gear_movement_id
 */
class GearMovementItem extends \yii\db\ActiveRecord
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
            [['id', 'gear_item_id', 'gear_movement_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_movement_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_item_id' => 'Gear Item ID',
            'gear_movement_id' => 'Gear Movement ID',
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\GearMovementItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\GearMovementItemQuery(get_called_class());
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItem()
    {
        return $this->hasOne(\common\models\GearItem::className(), ['id' => 'gear_item_id']);
    }
      
}
