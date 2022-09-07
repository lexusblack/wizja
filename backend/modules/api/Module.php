<?php

namespace backend\modules\api;
use Yii;
use yii\web\Response;

/**
 * Module api definition class
 */
class Module extends \yii\base\Module {
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
//	    \Yii::$app->response->on(Response::EVENT_BEFORE_SEND, function ($event) {
//		    if ($user = Yii::$app->user->getIdentity()) {
//			    $response = $event->sender;
//			    /** @var \common\models\User $user */
//			    $token = $user->login_token;
//			    if ( is_array( $response->data ) ) {
//				    $response->data = [ 'items' => $response->data, 'token' => $token ];
//			    }
//			    else {
//				    $response->data = [ $response->data, 'token' => $token ];
//			    }
//		    }
//	    });
    }

}