<?php

namespace common\assets;

use yii\web\AssetBundle;


class UploaderAsset extends AssetBundle
{
  
  	public $sourcePath = '@bower/dropzone/dist';
  
  
    // public $basePath = '@webroot';
    // public $baseUrl = '@web';
    public $css = [
        'basic.css',
        'dropzone.css',
        'style.css',
    ];
    public $js = [
    	'dropzone.js',
        'common.js'
    ];
   
}
