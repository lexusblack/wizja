<?php

namespace common\assets;

use yii\web\AssetBundle;
use Yii;

class Gmap3Asset extends AssetBundle
{
    public $options = [];
    // nie ładuje
    //public $sourcePath = '@bower/gmap3/dist';
    public $sourcePath = '@common/assets/src/';

    public $js = [

        'gmap3/gmap3.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public function init() {
        parent::init();

        $key = @Yii::$app->params['gmaps.api.key'];
        $language = @Yii::$app->params['gmaps.api.language'];

        $this->options = array_merge($this->options, array_filter([
            'key' => $key,
            'language' => $language
        ]));
        // BACKWARD COMPATIBILITY

        $this->js[] = 'https://maps.googleapis.com/maps/api/js?'. http_build_query($this->options);
    }
}
