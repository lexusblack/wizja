<?php

namespace common\assets;

use yii\web\AssetBundle;



class PlanboardAsset extends AssetBundle
{
    // public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $sourcePath = '@common/assets/src/';
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'fullcalendar/fullcalendar.min.css',
        'css/planboard.css',
    ];
    public $js = [
        'fullcalendar/fullcalendar.js',
        'fullcalendar/locale/pl.js',
        'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
        'js/jquery.ui.touch-punch.min.js'
        // 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js',
        // 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-sanitize.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'omnilight\assets\MomentAsset',
    ];
}
