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
class AppAsset extends AssetBundle {

    public $sourcePath = '@vendor/jcabanillas/yii2-inspinia/assets';
    public $css = [
        'css/animate.css',
        'css/styleinspinia.css',
        'css/plugins/toastr/toastr.min.css',
        'css/plugins/blueimp/css/blueimp-gallery.min.css',
        "css/plugins/morris/morris-0.4.3.min.css",
        "css/plugins/sweetalert/sweetalert.css"
    ];
    public $js = [
        'js/plugins/metisMenu/jquery.metisMenu.js',
        'js/plugins/slimscroll/jquery.slimscroll.min.js',
        'js/inspinia.js',
        'js/plugins/pace/pace.min.js',
        'js/ajax-modal-popup.js',
        'js/ajax-modal-popup.js',
        'js/jspdf.debug.js',
        'js/vue.js',
        'js/plugins/toastr/toastr.min.js',
        'js/plugins/video/responsible-video.js',
        'js/plugins/blueimp/jquery.blueimp-gallery.min.js',
         "js/plugins/flot/jquery.flot.js",
        "js/plugins/flot/jquery.flot.tooltip.min.js",
        "js/plugins/flot/jquery.flot.resize.js",
        "js/plugins/flot/jquery.flot.pie.js",
        "js/plugins/flot/jquery.flot.time.js",
        "js/plugins/morris/raphael-2.1.0.min.js",
        "js/plugins/morris/morris.js",
        "js/plugins/nestable/jquery.nestable.js"
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'jcabanillas\inspinia\FontawesomeAsset'
    ];

}
