<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_value".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property string $section
 * @property string $value
 */
class OfferValue extends \yii\db\ActiveRecord
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
            [['offer_id'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_value';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_id' => 'Offer ID',
            'section' => 'Section',
            'value' => 'Value',
        ];
    }
}
