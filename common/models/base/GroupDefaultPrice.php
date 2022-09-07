<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "group_default_price".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $gears_price_id
 * @property integer $price_group_id
 */
class GroupDefaultPrice extends \yii\db\ActiveRecord
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
            [['gear_id', 'gears_price_id', 'price_group_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_default_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => 'Gear ID',
            'gears_price_id' => 'Gears Price ID',
            'price_group_id' => 'Price Group ID',
        ];
    }
}
