<?php

namespace common\models;

use Yii;
use \common\models\base\Company as BaseCompany;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "company".
 */
class Company extends BaseCompany
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'link', 'code'], 'required'],
            [['start_date'], 'safe'],
            [['name', 'link', 'code', 'mail', 'phone'], 'string', 'max' => 255]
        ]);
    }

    public function getAssignedErrors()
    {
        $query = $this->hasMany(\common\models\Request::className(), ['company_id' => 'code']);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedLocations()
    {
        $query = $this->hasMany(\common\models\Location::className(), ['owner' => 'code']);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }	

    public function getAssignedCrossRentals()
    {
        $query = $this->hasMany(\common\models\CrossRental::className(), ['owner' => 'code']);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public static function getOne($id)
    {
        return static::find()->where(['like', 'code', $id])->one();
    }

    public function getList()
    {
        return ArrayHelper::map(Company::find()->asArray()->all(), 'code', 'name');
    }

public function getLogoUrl()
    {
        $url = Yii::getAlias('@uploadsAll/'.$this->logo);
        return $url;
    }

    public function getLogo($class=""){
        if($this->logo) {
            return Html::img($this->getLogoUrl(), array('class'=>$class));
        }else{
            return Html::img('/admin/site/generate-photo?initial='.substr($this->name,0,3), array('class'=>$class));
        }
        
    }
}
