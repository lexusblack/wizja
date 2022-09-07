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
abstract class CRCategory extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_category';
    }

    public static function getDb() {
        return Yii::$app->db2;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
            [['lft', 'rgt', 'lvl', 'name'], 'required'],
            [['name', 'color'], 'string', 'max' => 60],
            [['icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Unikatowy identyfikator węzła drzewa'),
            'root' => Yii::t('app', 'Identyfikator korzenia drzewa'),
            'lft' => Yii::t('app', 'Odziedziczony zestaw właściwości z lewej strony'),
            'rgt' => Yii::t('app', 'Odziedziczony zestaw właściwości z prawej strony'),
            'lvl' => Yii::t('app', 'Poziom odziedziczonego zestawu / głębokość'),
            'name' => Yii::t('app', 'Nazwa węzła drzewa / etykieta'),
            'icon' => Yii::t('app', 'Ikona węzła'),
            'icon_type' => Yii::t('app', 'Typ ikony: 1 = Klasa CSS, 2 = surowy znacznik'),
            'active' => Yii::t('app', 'Czy węzeł jest aktywny'),
            'selected' => Yii::t('app', 'Czy domyślnie węzeł jest wybrany/zaznaczony'),
            'disabled' => Yii::t('app', 'Czy węzeł jest włączony'),
            'readonly' => Yii::t('app', 'Czy węzeł jest tylko do odczytu'),
            'visible' => Yii::t('app', 'Czy węzeł jest widzialny'),
            'collapsed' => Yii::t('app', 'Czy domyślnie węzeł jest zwinięty'),
            'movable_u' => Yii::t('app', 'Czy można przesunąć węzeł jedną pozycję do góry'),
            'movable_d' => Yii::t('app', 'Czy można przesunąć węzeł jedną pozycję w dół'),
            'movable_l' => Yii::t('app', 'Czy można przesunąć węzeł w lewo'),
            'movable_r' => Yii::t('app', 'Czy można przesunąć węzeł w prawo'),
            'removable' => Yii::t('app', 'Czy można usunąć węzeł'),
            'removable_all' => Yii::t('app', 'Czy można usunąć węzeł ze wszystkimi potomkami'),
        ];
    }

}
