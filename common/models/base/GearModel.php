<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "gear_model".
 *
 * @property integer $id
 * @property string $name
 * @property string $brightness
 * @property string $power_consumption
 * @property integer $type
 * @property integer $category_id
 * @property string $width
 * @property string $height
 * @property string $volume
 * @property string $depth
 * @property string $weight
 * @property string $weight_case
 * @property string $info
 * @property string $photo
 * @property string $create_time
 * @property string $update_time
 * @property integer $company_id
 *
 * @property \common\models\GearCategory $category
 * @property \common\models\GearCompany $company
 * @property \common\models\GearModelAttachment[] $gearModelAttachments
 * @property string $aliasModel
 */
abstract class GearModel extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_model';
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
            [['name'], 'required'],
            [['brightness', 'power_consumption', 'width', 'height', 'volume', 'depth', 'weight', 'weight_case'], 'number'],
            [['type', 'category_id', 'company_id'], 'integer'],
            [['info'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'photo'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearCompany::className(), 'targetAttribute' => ['company_id' => 'id']]
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
            'brightness' => Yii::t('app', 'Jasno???? [lm]'),
            'power_consumption' => Yii::t('app', 'Pob??r pr??du [W]'),
            'type' => Yii::t('app', 'Typ'),
            'category_id' => Yii::t('app', 'Kategoria'),
            'company_id' => Yii::t('app', 'Producent'),
            'width' => Yii::t('app', 'Szeroko???? [cm]'),
            'height' => Yii::t('app', 'Wysoko???? [cm]'),
            'volume' => Yii::t('app', 'Obj??to???? [m3]'),
            'depth' => Yii::t('app', 'G????boko???? [cm]'),
            'weight' => Yii::t('app', 'Waga [kg]'),
            'weight_case' => Yii::t('app', 'Waga w case [kg]'),
            'info' => Yii::t('app', 'Opis'),
            'photo' => Yii::t('app', 'Zdj??cie'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\common\models\GearModelCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(\common\models\GearCompany::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearModelAttachments()
    {
        return $this->hasMany(\common\models\GearModelAttachment::className(), ['gear_model_id' => 'id']);
    }




}
