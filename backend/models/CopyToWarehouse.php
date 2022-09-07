<?php
namespace backend\models;

use Yii;
use yii\base\Model;

class CopyToWarehouse extends Model {

    public $category_id;
    public $gear_id;
    public $copy_gear_with_items;


    public function rules()
    {
        return [
            [['category_id', 'gear_id', 'copy_gear_with_items'], 'integer'],
            [['category_id'], 'required'],
            [['gear_id'], 'required', 'on' => 'gearItem'],
        ];
    }

    public function attributeLabels()
    {
        $labels = [
            'category_id' => Yii::t('app', 'Kategorie'),
            'gear_id' => Yii::t('app', 'Modele'),
            'copy_gear_with_items' => Yii::t('app', 'dodaj ze wszystkimi elementami')
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function copyToGearItem($outerGearItemID=null,$outerGearItemModel=null)
    {   
        if(!$outerGearItemID && !$outerGearItemModel){
            return false;
        } elseif($outerGearItemModel){
            $outerGearItem = $outerGearItemModel;
        } elseif($outerGearItemID){
            $outerGearItem = \common\models\OuterGearItem::findOne($outerGearItemID);
        }

        $model = new \common\models\GearItem([
            'attributes' => $outerGearItem->getAttributes()
        ]);
        $model->gear_id = $this->gear_id;
        $model->id = null;
        return $model->save();
    }

    public function copyToGear($outerGearID)
    {
        $outerGear = \common\models\OuterGear::findOne($outerGearID);
        $model = new \common\models\Gear([
            'attributes' => $outerGear->getAttributes()
        ]);
        $model->id = null;
        $model->sort_order = 999;
        return $model;
    }
}