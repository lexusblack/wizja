<?php


namespace common\assets;

use yii\web\AssetBundle;

class TreeTableAsset extends AssetBundle  {

    public $sourcePath = '@common/assets/src/jquery-treetable';

    public $css = [
        'css/jquery.treetable.css',
        'css/jquery.treetable.theme.default.css',
    ];
    public $js = [
        'jquery.treetable.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}