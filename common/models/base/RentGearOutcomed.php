<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "rent_gear_outcomed".
 *
 * @property integer $rent_id
 * @property integer $gear_id
 * @property integer $quantity
 */
class RentGearOutcomed extends \yii\db\ActiveRecord
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
            [['rent_id', 'gear_id', 'quantity'], 'required'],
            [['rent_id', 'gear_id', 'quantity'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_gear_outcomed';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rent_id' => 'Rent ID',
            'gear_id' => 'Gear ID',
            'quantity' => 'Quantity',
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
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
}
