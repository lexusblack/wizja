<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gears_price".
 *
 * @property integer $id
 * @property string $name
 * @property integer $gear_id
 * @property integer $gear_category_id
 * @property string $currency
 * @property integer $type
 *
 * @property \common\models\GearPrice[] $gearPrices
 */
class GearsPrice extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gearPrices'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'gear_category_id', 'type'], 'integer'],
            [['name', 'currency'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gears_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Stawka dla całego magazynu'),
            'gear_id' => Yii::t('app', 'Nazwa sprzętu'),
            'gear_category_id' => Yii::t('app', 'Kategoria'),
            'currency' => Yii::t('app', 'Waluta'),
            'type' => Yii::t('app', 'Typ'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearPrices()
    {
        return $this->hasMany(\common\models\GearPrice::className(), ['gears_price_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearsPricePercents()
    {
        return $this->hasMany(\common\models\GearsPricePercent::className(), ['gears_price_id' => 'id'])->orderBy(['day'=>SORT_ASC]);
    }

/**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
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
    public function getPriceGroups()
    {
        return $this->hasMany(\common\models\PriceGroup::className(), ['id' => 'price_group_id'])->viaTable('gears_price_group', ['gears_price_id' => 'id']);
    }

    }
