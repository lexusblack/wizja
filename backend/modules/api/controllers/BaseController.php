<?php

namespace backend\modules\api\controllers;

use common\models\User;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;

class BaseController extends ActiveController {
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'auth' => function ($username, $password) {
                $user = null;
                if ($username) {
                    $user = User::find()->where(['username' => $username])->one();
                }
                if ($user && $password && $user->validatePassword($password)) {
                    return $user;
                }
                return null;
            },
        ];
        return $behaviors;
    }
}