<?php
define('UNDEFINDED_STRING', 'UNDEFINED');

return [
	'name'=>'Wydarzenia',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=>'pl',
    'sourceLanguage'=>'pl',
    'aliases'=> [
        '@uploadroot'=>'@frontend/web/uploads',
        '@uploads'=>'/uploads',
        '@uploadrootAll'=>'@frontend/web/files',
        '@uploadsAll'=>'/files'
    ],
    'bootstrap' => [
        'languagepicker',
    ],
    'components' => [
        'formatter'=> [
            //'defaultTimeZone' => 'Europe/Warsaw',
            'nullDisplay'=>'-',
            'currencyCode'=>'PLN',
            'class' => 'common\components\EventsFormatter'
        ],
        'mailer' => require(__DIR__ . '/mailer.php'),
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
             //   ['class' => 'yii\rest\UrlRule', 'controller' => ['api/user']],
            ]
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'languagepicker' => [
//            'class' => 'lajax\languagepicker\Component',
            'class'=>'common\components\LanguageComponent',
            'languages' => function()
            {
                return \common\models\Language::getCodesList();
            }
        ],
        'assetManager' => [
            'bundles' => [
                'wbraganca\dynamicform\DynamicFormAsset' => [
                     'sourcePath' => '@frontend/web/admin/js',
                     'js' => ['yii2-dynamic-form.js']
                ],
                'kartik\daterange\DateRangePickerAsset' => [
                     'sourcePath' => '@frontend/web/admin/daterangepicker',
                     'js' => ['js/daterangepicker.js'],
                     'css' => ['css/daterangepicker-kv.min.css', 'css/daterangepicker.min.css' ],
                ],
                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => 'AIzaSyBERPdMJZVbYlTm3XWu0x5tqM3--Ojwb4Y',
                        'language' => 'pl',
//                        'version' => '3.1.18'
                    ]
                ],
            ],
        ],
        'settings' => [
            'class' => 'pheme\settings\components\Settings'
        ],
        'thumbnail' => [
            'class'=>'sadovojav\image\Thumbnail',
            'cachePath' => '@uploadroot/thumbs',
            'prefixPath' => '@uploads',
        ],
        'i18n' => [
            'translations'=>[
                'extensions/yii2-settings/*'=>[
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en',
                    'basePath' => '@vendor/pheme/yii2-settings/messages',
                    'fileMap' => [
                        'extensions/yii2-settings/settings' => 'settings.php',
                    ],
                ],
                'kvenum' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage'=>'en',
                    'basePath'=>'@common/messages',
                    'fileMap' => [
                        'kvenum' => 'kvenum.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'app*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceLanguage' => 'pl',
//                    'class' => 'yii\i18n\PhpMessageSource',
//                    'basePath' => '@common/messages',
//                    'fileMap' => [
//                        'app' => 'app.php',
//                        'app/error' => 'error.php',
//                    ],
                ],
            ],
        ],
        'user' => [
            'class'=>'common\components\User',
        ]
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],
       'dynagrid'=> [
            'class'=>'\kartik\dynagrid\Module',
            // other module settings
        ],
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'displaySettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:d.m.Y',
                \kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:d.m.Y H:i:s',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
                \kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
//            'displayTimezone' => 'Asia/Kolkata',
            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => ['pluginOptions'=>['autoclose'=>true]], // example
            ],
        ],
        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@uploadroot',
            'uploadUrl' => '@uploads',
//            'imageAllowExtensions'=>['jpg','png','gif']
        ],
    ]
];
