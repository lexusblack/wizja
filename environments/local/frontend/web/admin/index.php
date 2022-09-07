<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$adminBaseDir = __DIR__ . '/../../../backend';
require($adminBaseDir . '/../vendor/autoload.php');
require($adminBaseDir . '/../vendor/yiisoft/yii2/Yii.php');
require($adminBaseDir . '/../common/config/bootstrap.php');
require($adminBaseDir . '/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require($adminBaseDir . '/../common/config/main.php'),
    require($adminBaseDir . '/../common/config/main-local.php'),
    require($adminBaseDir . '/config/main.php'),
    require($adminBaseDir . '/config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
