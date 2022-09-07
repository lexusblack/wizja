<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gears_price_percent".
 *
 * @property integer $id
 * @property integer $gears_price_id
 * @property string $value
 * @property integer $day
 */
class GearsPricePercent extends \yii\db\ActiveRecord
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
            [['gears_price_id', 'day'], 'integer'],
            [['value'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gears_price_percent';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gears_price_id' => 'Gears Price ID',
            'value' => 'Value',
            'day' => 'Day',
        ];
    }
}
