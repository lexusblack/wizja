<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class StatsForm extends Model
{
    public $y;
    public $m;
    public $category_id;


    public function rules()
    {
        return [
            [['date_from', 'date_to'], 'string'],
            [['date_from', 'date_to'], 'integer'],
        ];
    }

        public function attributeLabels()
    {
        $labels = [
            'date_from' => Yii::t('app', 'Data od'),
            'date_to' => Yii::t('app', 'Data do'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

}