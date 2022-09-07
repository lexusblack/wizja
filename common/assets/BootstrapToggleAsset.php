<?php


namespace common\assets;

use yii\web\AssetBundle;

class BootstrapToggleAsset extends AssetBundle {
    public $sourcePath = '@common/assets/src/bootstrap-toggle';

    public $css = [
        'css/bootstrap2-toggle.css',
    ];
    public $js = [
        'js/bootstrap2-toggle.js',
    ];
    public $depends = [
        'common\assets\AppAsset',
        'yii\web\JqueryAsset',
    ];
}