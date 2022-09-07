<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class CopyFromEventForm extends Model
{
    public $event_to;
    public $event_copy;
    public $type;


    public function rules()
    {
        return [
            [['type'], 'string'],
            [['event_to', 'event_copy'], 'integer'],
        ];
    }

        public function attributeLabels()
    {
        $labels = [
            'event_copy' => Yii::t('app', 'Wybierz wydarzenie'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

}