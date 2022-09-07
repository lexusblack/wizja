<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "offer_gear_setting".
 *
 * @property integer $gear_category_id
 * @property integer $offer_id
 * @property integer $duration
 * @property integer $next_day_percent
 * @property integer $type
 *
 * @property \common\models\Offer $offer
 * @property \common\models\GearCategory $gearCategory
 * @property string $aliasModel
 */
abstract class OfferGearSetting extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_gear_setting';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_category_id', 'offer_id', 'duration', 'next_day_percent', 'type'], 'integer'],
            [['offer_id'], 'required'],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::className(), 'targetAttribute' => ['offer_id' => 'id']],
            [['gear_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearCategory::className(), 'targetAttribute' => ['gear_category_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gear_category_id' => Yii::t('app', 'ID kategorii sprzętu'),
            'offer_id' => Yii::t('app', 'ID oferty'),
            'duration' => Yii::t('app', 'Długość'),
            'next_day_percent' => Yii::t('app', 'Procent dnia następnego'),
            'type' => Yii::t('app', 'Typ'),
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
    public function getGearCategory()
    {
        return $this->hasOne(\common\models\GearCategory::className(), ['id' => 'gear_category_id']);
    }




}