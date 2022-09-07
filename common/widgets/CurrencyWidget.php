<?php
namespace common\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Inflector;

class CurrencyWidget extends Widget
{
    public function run()
    {

        $ch=curl_init();
        $timeout=5;

        curl_setopt($ch, CURLOPT_URL, "https://api.nbp.pl/api/exchangerates/tables/c");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $json_string=curl_exec($ch);
        curl_close($ch);

        $parsed_json = json_decode($json_string);
        if (json_last_error() === JSON_ERROR_NONE) {
                // JSON is valid

        }else{
             return null;
        }
        $rates = $parsed_json[0]->rates;
//    \yii\helpers\VarDumper::dump($rates, 5,true);


        $formatter = \Yii::$app->formatter;
        $content = '';
        $content.= Html::beginTag('div',["class" => "currency_widget"]);
            $content .=  Html::tag('h6', Yii::t('app', 'Kursy walut'));
        $content .= Html::beginTag('table');
        $content .= Html::beginTag('tr');
        $content .= Html::tag('td', '<i class="en-US"></i>'.$rates[0]->code, ['class'=>'text-center language-picker small']);
        $content .= Html::tag('td', $formatter->asCurrency($rates[0]->bid).' / '.$formatter->asCurrency($rates[0]->ask));
        $content .= Html::endTag('tr');
        $content .= Html::beginTag('tr');
        $content .= Html::tag('td', '<i class="eu"></i>'.$rates[3]->code, ['class'=>'text-center language-picker small']);
        $content .= Html::tag('td', $formatter->asCurrency($rates[3]->bid).' / '.$formatter->asCurrency($rates[3]->ask));
        $content .= Html::endTag('tr');
        $content .= Html::beginTag('tr');
        $content .= Html::tag('td',  '<i class="en"></i>'.$rates[6]->code, ['class'=>'text-center language-picker small']);
        $content .= Html::tag('td', $formatter->asCurrency($rates[6]->bid).' / '.$formatter->asCurrency($rates[6]->ask));
        $content .= Html::endTag('tr');
        $content .= Html::endTag('table');
        $content .= Html::endTag('div');
        return $content;
    }
}