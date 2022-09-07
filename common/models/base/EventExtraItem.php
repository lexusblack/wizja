<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_extra_item".
 *
 * @property integer $id
 * @property integer $offer_extra_item_id
 * @property string $name
 * @property integer $quantity
 * @property integer $gear_category_id
 * @property string $weight
 * @property string $volume
 *
 * @property \common\models\GearCategory $gearCategory
 * @property \common\models\OfferExtraItem $offerExtraItem
 */
class EventExtraItem extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gearCategory',
            'offerExtraItem'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_extra_item_id', 'quantity', 'gear_category_id'], 'integer'],
            [['weight', 'volume'], 'number'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_extra_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_extra_item_id' => 'Offer Extra Item ID',
            'name' => 'Nazwa',
            'quantity' => 'Ilość',
            'gear_category_id' => 'Sekcja',
            'weight' => 'Waga [kg]',
            'volume' => 'Objętość [m3]',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearCategory()
    {
        return $this->hasOne(\common\models\GearCategory::className(), ['id' => 'gear_category_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferExtraItem()
    {
        return $this->hasOne(\common\models\OfferExtraItem::className(), ['id' => 'offer_extra_item_id']);
    }

    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
    }
