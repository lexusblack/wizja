<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "cross_rental".
 *
 * @property integer $id
 * @property string $owner
 * @property integer $gear_model_id
 * @property string $owner_name
 * @property string $owner_city
 * @property string $price
 * @property integer $owner_gear_id
 * @property integer $quantity
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\GearModel $gearModel
 */
class CrossRental extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_model_id', 'owner_gear_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['owner', 'owner_city'], 'string', 'max' => 45],
            [['owner_name'], 'string', 'max' => 255],
            [['price', 'quantity'], 'required']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cross_rental';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'owner' => Yii::t('app','Właściciel'),
            'gear_model_id' => Yii::t('app','Nazwa'),
            'owner_name' => Yii::t('app','Wypożyczający'),
            'owner_city' => Yii::t('app','Miasto'),
            'owner_country' => Yii::t('app','Kraj'),
            'price' => Yii::t('app','Orientacyjna cena'),
            'owner_gear_id' => Yii::t('app','Sprzęt z magazynu'),
            'quantity' => Yii::t('app','Liczba'),
            'create_time' => Yii::t('app','Stworzono'),
            'update_time' => Yii::t('app','Zaktualizowano'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearModel()
    {
        return $this->hasOne(\common\models\GearModel::className(), ['id' => 'gear_model_id']);
    }

    public function getCompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['code' => 'owner']);
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
