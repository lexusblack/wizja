<?php
namespace common\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Inflector;

class WeatherWidget extends Widget
{
    public function run()
    {
    	$lat = null;
    	$lon = null;
	    $ch=curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/".Yii::$app->request->getUserIP());
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    $json_string=curl_exec($ch);
	    curl_close($ch);
	    if (json_last_error() === JSON_ERROR_NONE) {
		    $parsed_json = json_decode($json_string);
		    if (property_exists($parsed_json, 'lat') && property_exists($parsed_json, 'lon')) {
			    $lat = $parsed_json->lat;
			    $lon = $parsed_json->lon;
		    }
	    }

        if (Yii::$app->settings->get('companyCity', 'main')!="")
        {
            $city = Yii::$app->settings->get('companyCity', 'main');
        }else {
            $city = "Warszawa";
        }

        if ($lat != null && $lon != null) {
	        $url = 'http://api.wunderground.com/api/0fd87793834f4623/forecast10day/lang:PL/q/'.$lat.','.$lon.'.json';
        }
        else {
	        $url = 'http://api.wunderground.com/api/0fd87793834f4623/forecast10day/lang:PL/q/Pl/' . $city . '.json';
        }

        $ch=curl_init();
        $timeout=5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $json_string=curl_exec($ch);
        curl_close($ch);

        $parsed_json = json_decode($json_string);
        if (json_last_error() === JSON_ERROR_NONE) {
                // JSON is valid
            //echo $json_string;
           // exit;
        }else{
             return null;
        }
        $content = Html::beginTag('div', ["class" => "weather_widget" ]);

            $content .= Html::beginTag('div', ["class" => "weather_days" ]);

            for ($i=0; $i < 7; $i++) { 
                $forecast = $parsed_json->forecast->simpleforecast->forecastday[$i];
                $formatter = \Yii::$app->formatter;
                $day = $formatter->asDate($forecast->date->epoch, 'php:l');
                if($i == 0){
                    $day = Yii::t('app', 'Dzisiaj');
                } elseif($i == 1) {
                    $day = Yii::t('app', 'Jutro');
                }
                $content .= '<div class="one_day">';
                $content .= Html::tag('h6', $day );

                $local_image_url = '/admin/themes/e4e/images/weather/'.$forecast->icon.'.png';

                if(file_exists(\Yii::getAlias('@frontend').'/web'.$local_image_url)){
                    $content .= Html::img($local_image_url); 
                } else {
                    $content .= Html::img($forecast->icon_url);
                }
                $content .= '</br>';
                $content .= $forecast->high->celsius.'&deg; '.$forecast->low->celsius.'&deg;';
                $content .= '</div>';
            }

            $content .= Html::endTag('div');

        

            $content .= Html::beginTag('div', ["class" => "weather_details"]);
                $forecast = $parsed_json->forecast->simpleforecast->forecastday[0];

                $content .= Html::tag('div', Yii::t('app', 'Wilgotność').': '.$forecast->avehumidity.'%' );
                $content .= Html::tag('div', Yii::t('app', 'Wiatr').': '.$forecast->avewind->kph.'km/h' );
                $content .= Html::tag('div', Yii::t('app', 'Szansa opadów').': '.$forecast->pop.'%' );
            $content .= Html::endTag('div');

        $content .= Html::endTag('div');
        return $content;
    }

}