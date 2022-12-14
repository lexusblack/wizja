<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "gear_category".
 *
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $lvl
 * @property string $name
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 *
 * @property \common\models\CustomerDiscountCategory[] $customerDiscountCategories
 * @property \common\models\CustomerDiscount[] $customerDiscounts
 * @property \common\models\Gear[] $gears
 * @property \common\models\GearModel[] $gearModels
 * @property \common\models\OfferGearSetting[] $offerGearSettings
 * @property \common\models\OfferSetting[] $offerSettings
 * @property \common\models\OuterGear[] $outerGears
 * @property string $aliasModel
 */
abstract class GearCategory extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_category';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
            [['lft', 'rgt', 'lvl', 'name'], 'required'],
            [['name', 'color', 'font_color'], 'string', 'max' => 60],
            [['icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Unikatowy identyfikator w??z??a drzewa'),
            'root' => Yii::t('app', 'Identyfikator korzenia drzewa'),
            'lft' => Yii::t('app', 'Odziedziczony zestaw w??a??ciwo??ci z lewej strony'),
            'rgt' => Yii::t('app', 'Odziedziczony zestaw w??a??ciwo??ci z prawej strony'),
            'lvl' => Yii::t('app', 'Poziom odziedziczonego zestawu / g????boko????'),
            'name' => Yii::t('app', 'Nazwa w??z??a drzewa / etykieta'),
            'icon' => Yii::t('app', 'Ikona w??z??a'),
            'icon_type' => Yii::t('app', 'Typ ikony: 1 = Klasa CSS, 2 = surowy znacznik'),
            'active' => Yii::t('app', 'Czy w??ze?? jest aktywny'),
            'selected' => Yii::t('app', 'Czy domy??lnie w??ze?? jest wybrany/zaznaczony'),
            'disabled' => Yii::t('app', 'Czy w??ze?? jest w????czony'),
            'readonly' => Yii::t('app', 'Czy w??ze?? jest tylko do odczytu'),
            'visible' => Yii::t('app', 'Czy w??ze?? jest widzialny'),
            'collapsed' => Yii::t('app', 'Czy domy??lnie w??ze?? jest zwini??ty'),
            'movable_u' => Yii::t('app', 'Czy mo??na przesun???? w??ze?? jedn?? pozycj?? do g??ry'),
            'movable_d' => Yii::t('app', 'Czy mo??na przesun???? w??ze?? jedn?? pozycj?? w d????'),
            'movable_l' => Yii::t('app', 'Czy mo??na przesun???? w??ze?? w lewo'),
            'movable_r' => Yii::t('app', 'Czy mo??na przesun???? w??ze?? w prawo'),
            'removable' => Yii::t('app', 'Czy mo??na usun???? w??ze??'),
            'removable_all' => Yii::t('app', 'Czy mo??na usun???? w??ze?? ze wszystkimi potomkami'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscountCategories()
    {
        return $this->hasMany(\common\models\CustomerDiscountCategory::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerDiscounts()
    {
        return $this->hasMany(\common\models\CustomerDiscount::className(), ['id' => 'customer_discount_id'])->viaTable('customer_discount_category', ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGears()
    {
        return $this->hasMany(\common\models\Gear::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearModels()
    {
        return $this->hasMany(\common\models\GearModel::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferGearSettings()
    {
        return $this->hasMany(\common\models\OfferGearSetting::className(), ['gear_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferSettings()
    {
        return $this->hasMany(\common\models\OfferSetting::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGears()
    {
        return $this->hasMany(\common\models\OuterGear::className(), ['category_id' => 'id']);
    }




}
