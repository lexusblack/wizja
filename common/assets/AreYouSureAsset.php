<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class AreYouSureAsset extends AssetBundle
{
  
    public $sourcePath = '@bower/jquery.are-you-sure';

    public $js = [
        'jquery.are-you-sure.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
