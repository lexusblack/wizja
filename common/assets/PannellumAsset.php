<?php

namespace common\assets;

use yii\web\AssetBundle;


class PannellumAsset extends AssetBundle
{
  
//  	public $sourcePath = null;
//
//    public $baseUrl = 'https://cdn.pannellum.org/2.2/';
//
//    public $css = [
//        'pannellum.css',
//    ];
//    public $js = [
//        'pannellum.js',
//    ];

    public $sourcePath = '@bower/pannellum';

//    public $basePath = '@webroot';
//    public $baseUrl = '@web';

    public $css = [
        'css/pannellum.css',
    ];

    public $js = [
//        'js/RequestAnimationFrame.js',
        'js/libpannellum.js',
        'js/pannellum.js',
    ];

}
