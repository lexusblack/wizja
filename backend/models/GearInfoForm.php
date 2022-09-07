<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class GearInfoForm extends Model
{
    public $gear_item_id;
    public $info;


    public function rules()
    {
        return [
            [['info'], 'string'],
            [[ 'gear_item_id'], 'integer'],
        ];
    }

        public function attributeLabels()
    {
        $labels = [
            'gear_item_id' => Yii::t('app', 'Egzemplarz'),
            'info' => Yii::t('app', 'Treść'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

}