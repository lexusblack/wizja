<?php

namespace common\models;

use \common\models\base\OuterGearModel as BaseOuterGearModel;
use Yii;
use yii\data\ActiveDataProvider;
use sadovojav\image\Thumbnail;
use common\helpers\ArrayHelper;
use common\behaviors\WorkingTimeBehavior;


/**
 * This is the model class for table "outer_gear_model".
 */
class OuterGearModel extends BaseOuterGearModel
{

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>WorkingTimeBehavior::className(),
                'connectionClassName'=>EventOuterGearModel::className(),
                'itemIdAttribute'=>'outer_gear_model_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }
    public function getMainCategory()
    {
        $cat = $this->category;
        if ($cat->lvl==1)
        {
            return $cat;
        }else{
            return $cat->parents()->andWhere(['lvl'=>1])->one();
        }
    }
    /**
     * @inheritdoc
     */

    public function getTranslates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearTranslates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getItemsCompany()
    {
        $company = "";
        foreach ($this->outerGears as $item)
        {
            if ($item->company)
            {
                if ($item->active)
                    $company .= $item->company->name." (".$item->quantity." ".Yii::t('app', 'szt.').")<br/>";
            }
                                    
        }
        return $company;
    }

    public function getAssignedItems($params = [])
    {
        $params = array_merge(['active'=>1], $params);
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->hasMany(\common\models\OuterGear::className(), ['outer_gear_model_id' => 'id'])->andWhere(['active'=>1]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getIsGearAssigned($event,$model, $type=null, $item=null)
    {
        return $event->getAssignedOuterGearModel($event->id,$model->id, $type, $item);
    }

    public function getAssignedGearNumber($event, $model, $type=null, $item=null) {
        if (!$this->getIsGearAssigned($event, $model, $type, $item)) {
            return 0;
        }
        return $event->getAssignedOuterGearModelNumber($event->id, $model->id, $type, $item);
    }

    public function getQuantity()
    {
        $quantity = 0;
        foreach ($this->outerGears as $item)
        {
            if ($item->active)
                $quantity+=$item->quantity;
                                    
        }
        return $quantity;
    }

    public function getEventOuterGearIds()
    {
        $ids = [];
        foreach ($this->outerGears as $item)
        {
            if ($item->active)
                $ids[] = $item->id;
                                    
        }
        return $ids;
    }

    public function getSellingPrice()
    {
        $price = 0;
        foreach ($this->outerGears as $item)
        {
            if ($item->active)
                if ($item->selling_price>$price)
                $price = $item->selling_price;
                                    
        }
        return $price;
    }

    public function getPrice()
    {
        $price = 0;
        foreach ($this->outerGears as $item)
        {
            if ($item->active)
                if ($item->price>$price)
                    $price = $item->price;
                                    
        }
        return $price;
    }

    public function numberOfAvailable() {
            return $this->quantity;
    }

    public function getPhotoUrl()
    {
        if ($this->photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/outer-gear/'.$this->photo);
        }

    }


    public function getFileThumbUrl($options = [])
    {
        $defaultOptions = [
            'thumbnail' => [
                'width' => 200,
                'height' => 200,
                'mode'=>Thumbnail::THUMBNAIL_INSET,
            ],
            'placeholder' => [
                'width' => 200,
                'height' => 200
            ]
        ];
        $options = ArrayHelper::merge($defaultOptions, $options);
        try
        {
            $thumb = @Yii::$app->thumbnail->url($this->getFilePath(), $options);
        }
        catch (\Exception $e)
        {
            return null;
        }
        return $thumb;
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/outer-gear/'.$this->photo);
    }

    public function getCalculatedVolume()
    {
        $volume = $this->width * $this->height * $this->depth*0.000001;

        return $volume;
    }

    public function getMainCategoryName()
    {
        
    }

    public function countVolume()
    {
        $volume = $this->width * $this->height * $this->depth*0.000001;

        return $volume;
    }

    public static function getTranslateName($id, $language, $name)
    {
        if (!$language)
            return $name;
        $translate = OuterGearTranslate::find()->where(['language_id'=>$language])->andWhere(['gear_id'=>$id])->one();
        if ($translate)
            return $translate->name;
        else
            return $name;
    }
}
