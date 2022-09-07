<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class SendErrorMail extends Model
{
    public $location_id;
    public $subject;
    public $text;


    public function rules()
    {
        return [
            [['subject', 'text'], 'string'],
            [['subject',], 'required'],
            [['location_id'], 'integer']

        ];
    }

        public function attributeLabels()
    {
        $labels = [
            'subject' => Yii::t('app', 'Temat'),
            'text' => Yii::t('app', 'Treść'),
            'location_id' =>Yii::t('app', 'Miejsce')
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

}