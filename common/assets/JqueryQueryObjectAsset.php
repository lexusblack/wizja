<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class JqueryQueryObjectAsset extends AssetBundle
{
  
    public $sourcePath = '@bower/jquery-query-object';

    public $js = [
        'jquery.query-object.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
