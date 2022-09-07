<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
//    'theme'=>'e4e',
    'modules' => [
        'api' => [
            'basePath' => '@backend/modules/api',
            'class' => 'backend\modules\api\Module'
        ],
        'offer' => [
            'class' => 'backend\modules\offers\Module'
        ],
        'customer-discount' => [
            'class' => 'app\modules\customerDiscount\Module',
        ],
        'rbac' => [
            'class'=>'dektrium\rbac\RbacWebModule',
            'adminPermission'=>'SiteAdministrator',
        ],
        'settings' => [
            'class' => 'pheme\settings\Module',
            'sourceLanguage' => 'en'
        ],
        'permission' => [
            'class' => 'backend\modules\permission\Module',
        ],
        'settlement' => [
            'class' => 'backend\modules\settlement\Module',
        ],
        'tools' => [
            'class' => 'backend\modules\tools\Module',
        ],
        'finances' => [
            'class' => 'backend\modules\finances\Module',
        ],
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
        ],
    ],
    'components' => [
    	'urlManager' => [
            // here is your frontend URL manager config
            'class'=>'yii\web\UrlManager',
             'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'baseUrl'=>'/admin/',
            'rules'=>[
	            'api/device' => 'api/device/add',
	            'api/users/<id:\d+>/<action:[\w\-]+>' => 'api/user/<action>',
	            'api/users/<id:\d+>'  => 'api/user/user',
	            'api/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>/<name:\w+>' => 'api/<controller>/<action>',
	            'api/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>'  => 'api/<controller>/<action>'
            ],
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'=> [
                'name'=>'_backendUser',
                'path'=>'/admin/',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['sms*'],
                    'logFile'=>'@runtime/logs/sms.log',
                    'logVars'=>[],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],

            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-black',
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'basePath' => '@app/themes/e4e',
                'baseUrl' => '@web/themes/e4e',
                'pathMap' => [
                    '@app/views' => [
                        '@app/themes/e4e',
//                        '@app/views',
                    ]
                ],
            ],
        ],

    ],
    'params' => $params,
];
