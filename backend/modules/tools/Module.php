<?php

namespace backend\modules\tools;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * tools module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\tools\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app->user->can('SuperAdministrator') == false)
        {
            throw new ForbiddenHttpException();
        }
    }
}
