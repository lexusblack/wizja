<?php
namespace common\models\form;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;
use common\models\AgencyOffer;
use common\models\AgencyOfferServiceCategory;


class AgencyOfferForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $offer;
    public $serviceCategories = [];
    public $services;

    public function init()
    {
        if ($this->offer == null) {
            throw new HttpException(400, Yii::t('app', 'Nie ustawiono oferty.'));
        }
        $this->serviceCategories = $this->offer->getServiceData();
        $this->_setServices();
        parent::init();
    }

    public function rules()
    {
        $rules = [

            [['serviceCategories'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }

    public function loadValues()
    {

    }


    protected function _setServices()
    {
        $services = $this->offer->getAgencyOfferServices()->indexBy('id')->all();
        $this->services = $services;
    }

    public function loadAndSave()
    {

        $this->loadAndSaveServices();
    }


    public function loadAndSaveServices() {
        if (isset($_POST['AgencyOfferForm']['services'])) {
            $data = $_POST['AgencyOfferForm']['services'];

            if (Model::loadMultiple($this->services, $data, '') && Model::validateMultiple($this->services)) {
                foreach ($this->services as $obj) {
                    /* @var $obj \common\models\OfferVehicle */
                    $obj->total_price = $obj->count*$obj->price;
                    $obj->save();
                }
            }
        }
    }

}