<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use \common\models\Company;

/**
 * This is the base model class for table "location_plan".
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
class LocationPlan extends \yii\db\ActiveRecord
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

    public static function getDb() {
        return Yii::$app->db2;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_plan';
    }

     public static function find()
    {
        return new LocationPlanQuery(get_called_class());
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
            'location_id' =>  Yii::t('app', 'ID miejsca'),
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

    public function getFilePath()
    {
        if ($this->location->public!=2)
            return Yii::getAlias('@uploadroot/location-plan/'.$this->filename);
        else
            return Yii::getAlias('@uploadrootAll/location-plan/'.$this->filename);
    }
    public function getFileUrl()
    {
        if ($this->location->public!=2)
                    return Yii::getAlias('@uploads/location-plan/'.$this->filename);
                else
                    return Yii::getAlias('@uploadsAll/location-plan/'.$this->filename);
    }

    public function beforeSave($insert)
    {
        $this->owner = \Yii::$app->params['companyID'];
        $this->public = 0;
        return parent::beforeSave($insert);
    }

    public function getOwner()
    {
        if (($this->owner!="")&&($this->owner!="newsystem"))
        {
            $company = Company::find()->where(['code'=>$this->owner])->one();
            if ($company)
                return $company->name;
            else
                return 'New Event Management';
        }else{
            return 'New Event Management';
        }
    }
}
