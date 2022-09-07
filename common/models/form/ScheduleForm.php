<?php
namespace common\models\form;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class ScheduleForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $schedules = [];

    public function init()
    {


        parent::init();
    }

    public function rules()
    {
        $rules = [

            [['schedules'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }


}