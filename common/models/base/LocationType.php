<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "location_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $create_type
 * @property string $update_type
 *
 * @property \common\models\Location[] $locations
 */
abstract class LocationType extends \common\components\BaseActiveRecord
{
    public $existingModels = null;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 45]
        ];
    }
    

    public static function getDb() {
        return Yii::$app->db2;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_type';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'name' =>  Yii::t('app', 'Nazwa typu obiektu'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(\common\models\Location::className(), ['location_type_id' => 'id']);
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
            ],
        ];
    }
}
