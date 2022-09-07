<?php

namespace common\assets;

use yii\web\AssetBundle;



class VisJsAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/src/vis';

    public $css = [
        'dist/vis.min.css',
    ];
    public $js = [
        'dist/vis.js',
        'planboard/vis.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'omnilight\assets\MomentAsset',
    ];
    public $publishOptions = [
        'forceCopy'=>true,
      ];
}
