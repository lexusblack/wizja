<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gears_price_group".
 *
 * @property integer $id
 * @property integer $gears_price_id
 * @property integer $price_group_id
 */
class GearsPriceGroup extends \yii\db\ActiveRecord
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
            [['gears_price_id', 'price_group_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gears_price_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gears_price_id' => 'Gears Price ID',
            'price_group_id' => 'Price Group ID',
        ];
    }
}
