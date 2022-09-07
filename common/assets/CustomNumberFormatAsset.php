<?php

namespace common\assets;

use yii\web\AssetBundle;


class CustomNumberFormatAsset extends AssetBundle
{

    public $sourcePath = '@common/assets/src/custom-number-format';

    public $js = [
        'jquery.number.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
