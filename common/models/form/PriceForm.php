<?php
namespace common\models\form;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class PriceForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $prices = [];
    public $gears = [];
    public $defaults = [];
    public $group;

    public function init()
    {
        /*if ($this->gears == null) {
            throw new HttpException(400, Yii::t('app', 'Nie ustawiono kategorii.'));
        }*/
        $groups = \common\models\GearsPrice::find()->all();
        foreach ($this->gears as $gear)
        {
            foreach ($groups as $group){
                $price = \common\models\GearPrice::find()->where(['gear_id'=>$gear->id])->andWhere(['gears_price_id'=>$group->id])->one();
                if (!$price){
                    $price = new \common\models\GearPrice(['gear_id'=>$gear->id, 'gears_price_id'=>$group->id, 'price'=>0, 'cost'=>0, 'cost_name'=>Yii::t('app', 'Amortyzacja')]);
                }
                $this->prices[$gear->id][$price->gears_price_id] = $price; 
            }
        }
        foreach ($this->gears as $gear)
        {
            foreach ($groups as $group){
                $default = \common\models\GroupDefaultPrice::find()->where(['gear_id'=>$gear->id])->andWhere(['gears_price_id'=>$group->id])->andWhere(['price_group_id'=>$this->group])->one();
                if (!$default){
                    $default = new \common\models\GroupDefaultPrice(['gear_id'=>$gear->id, 'gears_price_id'=>$group->id, 'price_group_id'=>$this->group]);
                }
                $this->defaults[$gear->id][$group->id] = $default; 
            }
        }

        parent::init();
    }

    public function rules()
    {
        $rules = [

            [['gear'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }

    public function loadValues()
    {

    }

    public function loadAndSave()
    {
        $this->loadAndSaveGears();
    }


    public function loadAndSaveGears()
    {
        if (isset($_POST['PriceForm']['prices'])) {
            foreach ($_POST['PriceForm']['prices'] as $gear_id => $rows)
            {
            foreach ($rows as $group_id => $data)
            {
                $this->prices[$gear_id][$group_id]->price = $data['price'];
                $this->prices[$gear_id][$group_id]->cost = $data['cost'];
                $this->prices[$gear_id][$group_id]->cost_name = $data['cost_name'];
                $this->prices[$gear_id][$group_id]->one_per_event = $data['one_per_event'];
                $this->prices[$gear_id][$group_id]->save();
            }
            }
        }


        if (isset($_POST['PriceForm']['defaults'])) {
            foreach ($_POST['PriceForm']['defaults'] as $gear_id => $rows)
            {
            foreach ($rows as $group_id => $data)
            {
                if ($data['check']==1)
                {
                    $this->defaults[$gear_id][$group_id]->save();
                }else{
                    $default = \common\models\GroupDefaultPrice::find()->where(['gear_id'=>$gear_id])->andWhere(['gears_price_id'=>$group_id])->andWhere(['price_group_id'=>$this->group])->one();
                    if ($default)
                        $default->delete();

                }
            }
            }
        }
    }
}