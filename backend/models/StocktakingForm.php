<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class StocktakingForm extends Model
{
    public $gear;
    public $groups;
    public $items;


    public function rules()
    {
        return [
            [['date_from', 'date_to'], 'string'],
            [['date_from', 'date_to'], 'integer'],
        ];
    }

}