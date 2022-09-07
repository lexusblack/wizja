<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
//	'name'=>'Quizowanie',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\api\ApiModule',
        ],
    ],
    'components' => [
        'session' => [
            'name' => 'PHPFRONTSESSID',
            
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'=> [
                'name'=>'_frontendUser',
                'path'=>'/',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
				[
						'class' => 'yii\log\FileTarget',
						'categories' => ['api*'],
						'logFile'=>'@runtime/logs/api.log',
						'logVars'=>['_GET', '_POST'],
				],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'enableStrictParsing' => false,
                'rules' => [
                    ''=>'site/index',
                    [
                        'class' => 'yii\rest\UrlRule',
                        'pluralize'=>false,
                        'controller' => [
                            'api/answer',


                        ],
                    ],
//						[
//								'class' => 'yii\rest\UrlRule',
//								'pluralize'=>false,
//								'controller' => 'api/question-image',
//								'extraPatterns' => [
////										'POST upload' => 'upload',
////                        'GET recent'
//								]
//						],

                ],

        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-red',
                ],
            ],
        ],
       'request' => [
		    'parsers' => [
		        'application/json' => 'yii\web\JsonParser',
		    ]
		]
    ],
    'params' => $params,
];
