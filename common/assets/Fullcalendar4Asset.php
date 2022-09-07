<?php

namespace common\assets;

use yii\web\AssetBundle;



class Fullcalendar4Asset extends AssetBundle
{
    public $sourcePath = '@common/assets/src/fullcalendar-4.1.0';
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'packages/core/main.css',
        'packages/daygrid/main.css',
        'packages/timegrid/main.css',
        'packages/list/main.css',
//
    ];
    public $js = [
        'packages/core/main.js',
        'packages/interaction/main.js',
        'packages/daygrid/main.js',
        'packages/list/main.js',
        'packages/timegrid/main.js',
        'packages/core/locales-all.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'omnilight\assets\MomentAsset',
    ];
}
