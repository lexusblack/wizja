<?php
namespace common\models;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class OrderForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $contact_id;
    public $company_id;
    public $return;
    public $reception;
    public $eventOuterGear = [];

    public function rules()
    {
        $rules = [

            [['eventOuterGear', 'return'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }


}