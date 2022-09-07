<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SpaceplannerAsset extends AssetBundle {

    public $sourcePath = '@common/assets/src/spaceplanner';
    public $css = [
        'css/bootstrap.min.css',
        'css/font-awesome.min.css',
        'css/jquery-ui.min.css',
        'css/simple-sidebar.css',
        'css/cropper.min.css',
        'css/style.min.css',
        'css/jquery-confirm.min.css',
        'css/animate.css'
    ];
    public $js = [
        'js/jquery-3.2.1.min.js',
        'js/jquery-ui.min.js',
        'js/popper.min.js',
        'js/bootstrap.min.js',
        'js/cropper.min.js',
        'js/jquery.line.js',
        'js/jquery-confirm.min.js',
        'js/jquery.modalTabbing.js',
        'js/jquery.onmutate.min.js',
        'js/jspdf.min.js',
        'js/canvas2image.js',
        'js/js.cookie.js',
        'js/spaceplanner.js',
        'js/script.js'
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset'
    ];

}