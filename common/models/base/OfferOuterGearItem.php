<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "offer_outer_gear_item".
 *
 * @property integer $offer_id
 * @property integer $outer_gear_item_id
 * @property integer $quantity
 * @property integer $discount
 *
 * @property \common\models\Offer $offer
 * @property \common\models\OuterGearItem $outerGearItem
 * @property string $aliasModel
 */
abstract class OfferOuterGearItem extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_outer_gear_item';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'outer_gear_item_id'], 'required'],
            [['offer_id', 'outer_gear_item_id', 'quantity', 'discount'], 'integer'],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::className(), 'targetAttribute' => ['offer_id' => 'id']],
            [['outer_gear_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => OuterGearItem::className(), 'targetAttribute' => ['outer_gear_item_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'offer_id' =>  Yii::t('app', 'ID oferty'),
            'outer_gear_item_id' =>  Yii::t('app', 'ID sprzętu zewnętrznego'),
            'quantity' =>  Yii::t('app', 'Liczba'),
            'discount' =>  Yii::t('app', 'Rabat'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(\common\models\Offer::className(), ['id' => 'offer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearItem()
    {
        return $this->hasOne(\common\models\OuterGearItem::className(), ['id' => 'outer_gear_item_id']);
    }




}
