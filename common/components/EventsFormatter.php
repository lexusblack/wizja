<?php


namespace common\components;


use Yii;
use yii\i18n\Formatter;

class EventsFormatter extends Formatter {

    public function asCurrency($value, $currency = null, $options = [], $textOptions = []) {
        if (!$currency) {
            $currency = Yii::$app->settings->get('defaultCurrency', 'main');
        }
        return parent::asCurrency($value, $currency, $options, $textOptions);
    }
}