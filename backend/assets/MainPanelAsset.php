<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MainPanelAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/e4e';
    public $baseUrl = '@web/themes/e4e';
    public $css = [
        'css/site.css',
        'css/main-panel.css',
        'css/bootstrap-datetimepicker.css'
        // 'css/pdf_offer.css',
//        'css/style.css',
    ];
    public $js = [
        'js/maintainscroll.jquery.min.js',
        'js/common.js',
        '//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js',
        'js/bootstrap-datetimepicker.js'
        
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'jcabanillas\inspinia\AppAsset',
    ];
}
