<?php
namespace common\models\form;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class GearActionForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $items;
    public $action;

   
}