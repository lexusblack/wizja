<?php

namespace common\models;
use Yii;
use \common\models\base\LocationPanorama as BaseLocationPanorama;
use \common\models\Company;
/**
 * This is the model class for table "location_panorama".
 */
class LocationPanorama extends BaseLocationPanorama
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['status', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }
    public function getFilePath()
    {
        if ($this->location->public==2)
            return Yii::getAlias('@uploadroot/location-panorama/'.$this->filename);
        else
            return Yii::getAlias('@uploadrootAll/location-panorama/'.$this->filename);
    }
    public function getFileUrl()
    {
        if ($this->location->public==2)
                    return Yii::getAlias('@uploadsAll/location-panorama/'.$this->filename);
                else
                    return Yii::getAlias('@uploads/location-panorama/'.$this->filename);
    }

    public function getName()
    {
        if ($this->name!="")
            return $this->name;
        else
            return $this->filename;
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->public==1)
            EnNote::createNote('Panorama', $this);


    }
}
