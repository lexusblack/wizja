<?php
namespace backend\modules\tools\models;

use yii\base\Model;

class I18n extends Model
{
    public $text;
    public $lang;

    public function rules()
    {
        $rules = [
            ['lang', 'string'],
            ['text', 'safe']
        ];

        return array_merge(parent::rules(), $rules);
    }

    public static function getLanguageList()
    {
        return [
            'en'=>'EN',
            'de'=>'DE',
        ];
    }
}