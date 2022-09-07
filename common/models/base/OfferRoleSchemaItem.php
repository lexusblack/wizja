<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_role_schema_item".
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $price
 * @property integer $quantity
 * @property integer $duration
 * @property integer $time_type
 */
class OfferRoleSchemaItem extends \yii\db\ActiveRecord
{


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
            [['role_id', 'quantity', 'duration'], 'number'],
            [['price'], 'number'],
            [['time_type'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_role_schema_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'duration' => 'Duration',
            'time_type' => 'Time Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEventRole()
    {
        return $this->hasOne(\common\models\UserEventRole::className(), ['id' => 'role_id']);
    }
}
