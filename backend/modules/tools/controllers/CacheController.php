<?php
namespace backend\modules\tools\controllers;

use backend\components\Controller;

class CacheController extends Controller
{
    public function actionFlush()
    {
        \Yii::$app->cache->flush();
        return 'ok';
    }
}