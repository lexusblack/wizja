<?php

namespace common\models;

use Yii;
use \common\models\base\PriceGroup as BasePriceGroup;

/**
 * This is the model class for table "price_group".
 */
class PriceGroup extends BasePriceGroup
{
    
    public $gearsPriceIds;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'gearsPriceIds',
            ],
            'relations' => [
                'gearsPrices',
            ],
            'modelClasses'=>[
                'common\models\GearsPrice',
            ],
        ];

        return $behaviors;
    }

    public function getExcelData()
    {
        $gears = Gear::find()->where(['active'=>1])->all();
        $groups = $this->gearsPrices;
        $prices = [];
        foreach ($gears as $gear)
        {
            foreach ($groups as $group){
                $price = \common\models\GearPrice::find()->where(['gear_id'=>$gear->id])->andWhere(['gears_price_id'=>$group->id])->one();
                if (!$price){
                    $price = new \common\models\GearPrice(['gear_id'=>$gear->id, 'gears_price_id'=>$group->id, 'price'=>0, 'cost'=>0, 'cost_name'=>Yii::t('app', 'Amortyzacja')]);
                }
                $prices[$gear->id][$price->gears_price_id] = $price; 
            }
        }
        $data = [];
        $tmp = ["Nazwa sprzêtu", "Kategoria"];
        foreach ($groups as $group)
        {
            $tmp[] = $group->name;
        }
        $data[] = $tmp;
        foreach ($gears as $gear)
        {
            $tmp = [$gear->name, $gear->category->name];
            foreach ($groups as $group)
            {
                $tmp[] = $prices[$gear->id][$group->id]->price;
            }
            $data[]=$tmp;
        }
        return $data;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 45],
            [['gearsPriceIds'], 'each', 'rule'=>['integer']],
        ]);
    }
	
}
