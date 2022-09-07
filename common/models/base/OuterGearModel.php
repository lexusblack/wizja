<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "outer_gear_model".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $width
 * @property string $height
 * @property string $depth
 * @property string $weight
 * @property string $info
 * @property string $photo
 * @property string $create_time
 * @property string $update_time
 * @property string $power_consumption
 *
 * @property \common\models\OuterGear[] $outerGears
 * @property \common\models\GearCategory $category
 */
class OuterGearModel extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category_id'], 'required'],
            [['category_id', 'active', 'type'], 'integer'],
            [['width', 'depth', 'weight', 'height', 'power_consumption'], 'number'],
            [['info', 'unit'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'photo'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outer_gear_model';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'name' =>  Yii::t('app', 'Nazwa'),
            'category_id' =>  Yii::t('app', 'Kategoria'),
            'width' =>  Yii::t('app', 'Szerokość [cm]'),
            'height' =>  Yii::t('app', 'Wysokość [cm]'),
            'depth' =>  Yii::t('app', 'Głębokość [cm]'),
            'weight' =>  Yii::t('app', 'Waga [kg]'),
            'info' =>  Yii::t('app', 'Info'),
            'photo' =>  Yii::t('app', 'Zdjęcie'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
            'power_consumption' => Yii::t('app', 'Pobór prądu'),
            'unit'=>Yii::t('app', 'Jednostka'),
            'type'=> Yii::t('app', 'Typ')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGears()
    {
        return $this->hasMany(\common\models\OuterGear::className(), ['outer_gear_model_id' => 'id']);
    }
    public function getOfferOuterGears()
    {
        return $this->hasMany(\common\models\OfferOuterGear::className(), ['outer_gear_model_id' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearFavorite()
    {
        return $this->hasOne(\common\models\OuterGearFavorite::className(), ['outer_gear_id' => 'id'])->andWhere(['user_id'=>Yii::$app->user->id]);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\common\models\GearCategory::className(), ['id' => 'category_id']);
    }


    public function getGearTranslates()
    {
        return $this->hasMany(\common\models\OuterGearTranslate::className(), ['gear_id' => 'id']);
    }
    
/**
     * @inheritdoc
     * @return array mixed
     */ 
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }
}
