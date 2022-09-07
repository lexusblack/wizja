<?php

namespace common\models;
use Yii;
use \common\models\base\GearModel as BaseGearModel;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
/**
 * This is the model class for table "gear_model".
 */
class GearModel extends BaseGearModel
{
    /**
     * @inheritdoc
     */
    public function getPhotoUrl()
    {
        if ($this->photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploadsAll/gear/'.$this->photo);
        }

    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadrootAll/gear/'.$this->filename);
    }
    
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'required'],
            [['brightness', 'power_consumption', 'width', 'height', 'volume', 'depth', 'weight', 'weight_case'], 'number'],
            [['type', 'category_id'], 'integer'],
            [['info'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'photo'], 'string', 'max' => 255]
        ]);
    }
    
    public static function getSelectList() 
    {
        $query = self::find();
        
        $list = [];
        
        $models = $query->all();
        foreach ($models as $model) 
        {
            $list[$model->id] = $model->name;
            
        }
        
        return $list;
    }
    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearModelAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getDisplay()
    {
        if ($this->company)
            return "[".$this->company->name."] ".$this->name;
        else
            return $this->name;
    }
	
    public static function getList($term=null)
    {
        $models = static::find()
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->getDisplay();
        }

        return $list;
    }

}
