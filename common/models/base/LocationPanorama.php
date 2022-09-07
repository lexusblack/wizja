<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "location_panorama".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $location_id
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\Location $location
 */
class LocationPanorama extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ];
    }

     public static function find()
    {
        return new LocationPanoramaQuery(get_called_class());
    }

    public static function getDb() {
        return Yii::$app->db2;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_panorama';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'filename' =>  Yii::t('app', 'Nazwa pliku'),
            'extension' =>  Yii::t('app', 'Rozszerzenie'),
            'status' =>  Yii::t('app', 'Status'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
            'location_id' =>  Yii::t('app', 'Miejsce'),
            'mime_type' =>  Yii::t('app', 'Typ Mime'),
            'base_name' =>  Yii::t('app', 'Nazwa podstawowa'),
            'name' => Yii::t('app', 'Nazwa')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(\common\models\Location::className(), ['id' => 'location_id']);
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

    public function beforeSave($insert)
    {
        $this->owner = \Yii::$app->params['companyID'];
        $this->public = 0;
        return parent::beforeSave($insert);
    }
}
