<?php
namespace backend\modules\finances\models;

use common\components\SettingsTrait;
use yii\base\Model;

class SettingsForm extends Model
{
    use SettingsTrait;

    public $defaultCurrency;

    public function rules()
    {
        $rules = [
            [['defaultCurrency'], 'string'],
        ];
        return array_merge(parent::rules(), $rules);
    }

}