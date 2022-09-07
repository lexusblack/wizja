<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "gear_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 * @property integer $type
 * @property integer $status
 * @property integer $width
 * @property integer $height
 * @property integer $depth
 * @property integer $volume
 * @property string $warehouse
 * @property string $location
 * @property integer $weight
 * @property integer $active
 *
 * @property \common\models\Gear[] $gears
 * @property \common\models\GearItem[] $gearItems
 * @property \common\models\OuterGearItem[] $outerGearItems
 * @property string $aliasModel
 * @property string $rfid_code
 */
abstract class GearGroup extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_group';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['type', 'status', 'width', 'height', 'depth', 'active'], 'integer'],
            [['volume', 'weight'], 'number'],
            [['description', 'rfid_code', 'code'], 'string'],
            [['name', 'warehouse', 'location'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Nazwa'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'type' => Yii::t('app', 'Typ'),
            'status' => Yii::t('app', 'Status'),
            'width' => Yii::t('app', 'Szerokość [cm]'),
            'height' => Yii::t('app', 'Wysokość [cm]'),
            'depth' => Yii::t('app', 'Głębokość [cm]'),
            'volume' => Yii::t('app', 'Objętość [m3]'),
            'warehouse' => Yii::t('app', 'Magazyn'),
            'location' => Yii::t('app', 'Miejsce'),
            'weight' => Yii::t('app', 'Waga [kg]'),
            'description' => Yii::t('app', 'Opis'),
            'rfid_code' => Yii::t('app', 'Kod NEIS'),
            'code'=>Yii::t('app', 'Własny kod kreskowy'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGears()
    {
        return $this->hasMany(\common\models\Gear::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearItems()
    {
        return $this->hasMany(\common\models\GearItem::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearItems()
    {
        return $this->hasMany(\common\models\OuterGearItem::className(), ['group_id' => 'id']);
    }




}