<?php

namespace common\assets;

use yii\web\AssetBundle;



class FullcalendarAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/src/fullcalendar';
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'scheduler/lib/fullcalendar.min.css',
        'scheduler/scheduler.css',
//
    ];
    public $js = [
        'scheduler/lib/fullcalendar.min.js',
        'scheduler/scheduler.js',
        'locale/pl.js',
        'scheduler/lib/moment.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'omnilight\assets\MomentAsset',
    ];
}
