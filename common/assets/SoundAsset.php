<?php

namespace common\assets;

use yii\web\AssetBundle;



class SoundAsset extends AssetBundle
{
    // public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $sourcePath = '@common/assets/src/';
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $js = [
        'easyaudioeffects-master/jquery.easyaudioeffects.1.0.0.js'
        // 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js',
        // 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-sanitize.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
