<?php

namespace backend\modules\settlement\controllers;

use backend\components\Controller;
use common\models\Event;
use common\models\SettlementUser;
use common\models\User;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `settlement` module
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionStore()
    {
        foreach ($models as $event)
        {
            foreach ($event->users as $user)
            {
                SettlementUser::store($user->id, $event->id);
            }
        }
    }

}
