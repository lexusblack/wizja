<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_extra_cost".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property string $name
 * @property string $cost
 * @property integer $quantity
 * @property integer $offer_extra_item_id
 * @property string $section
 */
class OfferExtraCost extends \yii\db\ActiveRecord
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
            [['offer_id', 'quantity', 'offer_extra_item_id'], 'integer'],
            [['cost'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['section'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_extra_cost';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_id' => 'Offer ID',
            'name' => Yii::t('app', 'Nazwa'),
            'cost' => Yii::t('app', 'Koszt jednostkowy'),
            'quantity' => Yii::t('app', 'Ilość'),
            'offer_extra_item_id' => 'Offer Extra Item ID',
            'section' => Yii::t('app', 'Sekcja'),
        ];
    }
}
