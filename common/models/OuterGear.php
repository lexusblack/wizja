<?php

namespace common\models;

use common\models\IncomesGearOuter;
use common\models\OutcomesGearOuter;
use barcode\barcode\BarcodeGenerator;
use Imagine\Gd\Imagine;
use sadovojav\image\Thumbnail;
use Yii;
use \common\models\base\OuterGear as BaseOuterGear;
use common\behaviors\WorkingTimeBehavior;
use common\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "outer_gear".
 */
class OuterGear extends BaseOuterGear
{
	const TYPE_NORMAL = 1;
    const TYPE_NO_ITEM = 0;

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>WorkingTimeBehavior::className(),
                'connectionClassName'=>EventOuterGear::className(),
                'itemIdAttribute'=>'outer_gear_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }


    public function getPhotoUrl()
    {
        if ($this->outerGearModel->photo == null)
        {
            return null;
        }
        else
        {
            return Yii::getAlias('@uploads/outer-gear/'.$this->outerGearModel->photo);
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



    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public static function getSelectList() 
    {
        $query = self::find()->joinWith(['category'])->orderBy('category_id');
        
        $list = [];
        
        $models = $query->all();
        foreach ($models as $model) 
        {
            $list[$model->category->name][$model->id] = $model->name;
            
        }
        
        return $list;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['no_items']) && $this->no_items != $changedAttributes['no_items'])
        {
            $noItemsName = '_ILOSC_SZTUK_';
            if ($this->no_items==1)
            {
                $model = new OuterGearItem();
                $model->name = $noItemsName;
                $model->gear_id = $this->id;
                $model->type = 0;
                $model->save();
            }
            else
            {
                OuterGearItem::deleteAll([
                    'outer_gear_id' => $this->id,
                    'type' => 0,
                ]);
            }

        }
    }

    // public function getWorkingTime(){
    //     return [];
    // }

    public function getNoItemsItem()
    {
        return $this->getOuterGearItems()->where(['type'=>OuterGearItem::TYPE_NO_ITEM])->one();
    }   

    public function getIsGearAssigned($event,$model)
    {
        return $event->getAssignedOuterGear($event->id,$model->id);
    }

    public function getAssignedGearNumber($event, $model) {
        if (!$this->getIsGearAssigned($event, $model)) {
            return 0;
        }
        return $event->getAssignedOuterGearNumber($event->id, $model->id);
    }

    public function getCalculatedVolume()
    {
        $volume = $this->width * $this->height * $this->depth;

        return $volume;
    }

    public function generateBarCode() {
        $options = [
            'elementId' => 'bar-'.$this->id,
            'value' => $this->getBarCodeValue(),
            'type' => 'code128',
            'settings' => [
                'output' => 'bmp',
                'barWidth' => 1,
                'barHeight' => 50,
            ],
        ];
        return '<div id="bar-'.$this->id.'" data-name="' . $this->name . '"></div><div style="margin-top:-5px;text-align:center;font-size:9px;">' . $this->getBarCodeValue() . '</div>' . BarcodeGenerator::widget($options);
    }

    public function generateQrCode() {
        return Html::img(Url::to(['qr-code/get-img', 'text'=>$this->getBarCodeValue()]));
    }

    public function generateQrCodeAsLink() {
        return Html::a($this->generateQrCode(), Url::to(['qr-code/get-big-img', 'text'=>$this->getBarCodeValue()]), ['download' => $this->name . '.png', 'data-pjax' => '0', 'target' => '_blank']);
    }

    public function getBarCodeValue() {
        // 13 digits
        return BarCode::SINGEL_PRODUCT . BarCode::OUTER_WAREHOUSE . $this->getNineDigits();
    }

    private function getNineDigits() {
        $id_length = strlen($this->id);
        return str_repeat('0', 9-$id_length) . $this->id;
    }

    public function numberOfAvailable() {
        if ($this->quantity == null) {
            return 0;
        }

        $not_available = 0;
        $not_available_items = OutcomesGearOuter::find()->where(['outer_gear_id' => $this->id])->all();
        foreach ($not_available_items as $gear) {
            $not_available += $gear->gear_quantity;
        }

        $available = 0;
        $available_items = IncomesGearOuter::find()->where(['outer_gear_id' => $this->id])->all();
        foreach ($available_items as $gear) {
            $available += $gear->gear_quantity;
        }

        return $this->quantity - $not_available + $available;
    }

    public function attributeLabels()
    {
        $labels = [
            //'selling_price' => Yii::t('app', 'Cena Sprzedaży'),
            // zmiana nazwy wynika z blędu nr 317 http://r.softwebo.com/redmine/issues/317
            'selling_price' => Yii::t('app', 'Cena dla klienta'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getPlaceholderMap()
    {
        $map = [
            'gear.name' => $this->name,
        ];

        return $map;
    }

    public function getAssgignedEvents()
    {
        $query = $this->getEventOuterGears();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;        
    }

    public function getName()
    {
        if ($this->outerGearModel)
        {
            return $this->outerGearModel->name;
        }else{
            return '-';
        }
    }
    public function getPhoto()
    {
        
        if ($this->outerGearModel)
        {
            return $this->outerGearModel->photo;
        }else{
            return '-';
        }
    }
}
