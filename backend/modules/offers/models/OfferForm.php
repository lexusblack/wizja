<?php
namespace backend\modules\offers\models;
use common\helpers\ArrayHelper;
use common\models\Gear;
use common\models\GearCategory;
use common\models\Offer;
use common\models\OfferSetting;
use common\models\OfferVehicle;
use common\models\OuterGear;
use common\models\OuterGearModel;
use PhpParser\Node\Expr\AssignOp\Mod;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class OfferForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $offer;
    public $gear = [];
    public $outerGear = [];
    public $extraGear = [];
    public $allGears = [];
    public $vehicle = [];
    public $roles = [];

    public $gearSettings;
    public $gearModels;
    public $outerGearModels;
    public $vehicleModels;
    public $roleModels;
    public $comment;

    protected $_firstDayPercents;

    public function init()
    {
        if ($this->offer == null) {
            throw new HttpException(400, Yii::t('app', 'Nie ustawiono oferty.'));
        }
        $this->comment = $this->offer->comment;
        $this->gear = $this->offer->getGearData();
        $this->outerGear = $this->offer->getOuterGearData();
        $this->extraGear = $this->offer->getExtraGears();
        $this->allGears = $this->_mergeGears();
        $this->_setGearSettings();
        $this->_setGearModels();
        $this->_setOuterGearModels();
        $this->_setVehicles();
        $this->_setRoles();

        parent::init();
    }

    public function rules()
    {
        $rules = [

            [['gear', 'vehicle', 'roles', 'gearSettings', 'gearModels', 'outerGearModels', 'vehicleModels', 'roleModels'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }

    public function loadValues()
    {

    }

    protected function _setGearSettings()
    {
        $discountList = $this->offer->customer->getDiscountsList();
        $settings = OfferSetting::loadGear($this->offer->id);

        $gears = array_merge($this->outerGear, $this->gear);
        foreach ($gears as $categoryName => $data)
        {
            $category = GearCategory::loadMainByName($categoryName);
            $setting = null;
            if (isset($settings[$category->name]) == false)
            {
                $setting = new OfferSetting([
                    'type'=>OfferSetting::TYPE_GEAR,
                    'discount' => ArrayHelper::getValue($discountList, $category->id, 0),
                    'duration' => 1,
                    'first_day_percent' => Yii::$app->settings->get('firstDayPercent','offer', 50),
                    'category_id' =>$category->id,
                    'offer_id'=>$this->offer->id,
                ]);

                $setting->save();
                $settings[$category->name] = $setting;
            }
            else {
                $setting = $settings[$category->name];
            }

            $this->_firstDayPercents[$category->id] = $setting->first_day_percent;
        }


        $this->gearSettings = $settings;

    }

    protected function _setGearModels()
    {
        $gears = $this->offer->getOfferGears()->indexBy('id')->all();
        foreach ($gears as $gear) {
            if ($gear->price == null) {
                $gear->price = Gear::find()->where(['id' => $gear->gear_id])->one()->price;
            }
            if ($gear->price == null) {
                $gear->price = 0;
            }
        }
        $this->gearModels = $gears;
    }

    protected function _setOuterGearModels()
    {
        $gears = $this->offer->getOfferOuterGears()->indexBy('id')->all();
        foreach ($gears as $gear) {
            if ($gear->price == null) {
                $gear->price = OuterGearModel::find()->where(['id'=>$gear->outer_gear_model_id])->one()->getSellingPrice();
            }
        }
        $this->outerGearModels = $gears;
    }

    protected function _setVehicles()
    {
        $this->vehicle = $this->offer->getVehicleData();
        $this->vehicleModels = $this->offer->getOfferVehicles()->indexBy('id')->all();
    }

    protected function _setRoles()
    {
        $this->roles = $this->offer->roles;
        $this->roleModels = $this->offer->getOfferRoles()->indexBy('id')->orderBy(['time_type'=>SORT_ASC])->all();
    }

    protected function _mergeGears()
    {
        $return = array_merge_recursive($this->gear,$this->outerGear,$this->extraGear);
        $return2 = [];
        $mainCategories = \common\models\GearCategory::getMainList(true);
        foreach ($mainCategories as $c)
        {
            if (isset($return[$c->name]))
            {
                $return2[$c->name] = $return[$c->name];
            }
        }
		return $return2;
    }

    public function loadAndSave()
    {
        $this->loadAndSaveGearSettings();
        $this->loadAndSaveGears();
        $this->loadAndSaveVehicles();
        $this->loadAndSaveRoles();
        $this->loadAndSaveExtraItems();
        if (isset($_POST['OfferForm']['comment'])){
                    $this->comment = $_POST['OfferForm']['comment'];
                    $this->offer->comment = $this->comment;
                    $this->offer->save();
        }

    }

    public function loadAndSaveExtraItems() {
        if (isset($_POST['OfferExtraItem'])) {
            $data = $_POST;
            $models = $this->offer->getExtraItem();
            if (Model::loadMultiple($models, $data) && Model::validateMultiple($models)) {
                foreach ($models as $obj) {
                    if (!$obj->first_day_percent)
                        $obj->first_day_percent = $this->_firstDayPercents[$cat->id];
                    $obj->save();
                }
            }
        }
    }

    public function loadAndSaveVehicles() {
        if (isset($_POST['OfferForm']['vehicleModels'])) {
            $data = $_POST['OfferForm']['vehicleModels'];

            if (Model::loadMultiple($this->vehicleModels, $data, '') && Model::validateMultiple($this->vehicleModels)) {
                foreach ($this->vehicleModels as $obj) {
                    /* @var $obj \common\models\OfferVehicle */

                    $obj->save();
                }
            }
        }
    }

    public function loadAndSaveRoles() {
        if (isset($_POST['OfferForm']['roleModels'])) {
            $data = $_POST['OfferForm']['roleModels'];

            if (Model::loadMultiple($this->roleModels, $data, '') && Model::validateMultiple($this->roleModels)) {
                foreach ($this->roleModels as $obj) {
                    /* @var $obj \common\models\OfferRole */

                    $obj->save();
                }
            }
        }
    }

    public function loadAndSaveGears()
    {
        if (isset($_POST['OfferForm']['gearModels'])) {
            $data = $_POST['OfferForm']['gearModels'];
            if (Model::loadMultiple($this->gearModels, $data, '') && Model::validateMultiple($this->gearModels)) {
                foreach ($this->gearModels as $obj) {
                    /* @var $obj \common\models\OfferGear */

                    $cat = $obj->gear->category->getMainCategory();
                    if (!$obj->first_day_percent)
                        $obj->first_day_percent = $this->_firstDayPercents[$cat->id];
                    if (!$obj->save())
                    {
                        echo var_dump($obj);
                        exit;
                    }
                }
            }else{
                echo var_dump($this->gearModels);
                exit;
            }
        }

        if (isset($_POST['OfferForm']['outerGearModels'])) {
            $data = $_POST['OfferForm']['outerGearModels'];

            if (Model::loadMultiple($this->outerGearModels, $data, '') && Model::validateMultiple($this->outerGearModels)) {
                foreach ($this->outerGearModels as $obj) {
                    /* @var $obj \common\models\OfferOuterGear */

                    $cat = $obj->outerGearModel->category->getMainCategory();
                    if (!$obj->first_day_percent)
                        $obj->first_day_percent = $this->_firstDayPercents[$cat->id];
                    if (!$obj->save())
                    {
                        echo var_dump($obj);
                        exit;
                    }
                }
            }
        }
    }

    public function loadAndSaveGearSettings()
    {
        if (isset($_POST['OfferForm']['gearSettings'])) {
            if (Model::loadMultiple($this->gearSettings, $_POST['OfferForm']['gearSettings'], '') && Model::validateMultiple($this->gearSettings)) {
                foreach ($this->gearSettings as $obj) {
                    $obj->save();
                }
            }
        }
        $this->_setGearSettings();
    }
}