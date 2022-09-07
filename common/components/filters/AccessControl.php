<?php
namespace common\components\filters;

use common\models\User;
use Yii;
use yii\filters\AccessControl as BaseAccessControl;

class AccessControl extends BaseAccessControl
{
    public $baseName;
    public $otherActionRule = true;

    public function init()
    {
        $rules = [
            [
                'allow' => true,
                'roles' => ['SiteAdministrator']
            ],
        ];

        $this->rules = array_merge($this->rules, $rules);
        parent::init();
    }

    public function beforeAction( $action ) {
//    	$token = Yii::$app->request->get('access_token');
//    	if ($token) {
//    		if ($user = User::findByLoginToken($token)) {
//			    Yii::$app->user->login($user);
//			    $user->generateLoginToken();
//		    }
//		    $this->redirectWithoutToken($action);
//        }
		return parent::beforeAction( $action );
    }

    private function redirectWithoutToken($action) {
	    $url = parse_url(Yii::$app->request->getUrl());
	    parse_str($url['query'], $query);
	    unset($query['access_token']);
	    $newUrl = $url['path'];
	    $i = 1;
	    foreach ($query as $param => $value){
		    if ($i == 1) {
			    $newUrl .= "?";
		    }
		    else {
			    $newUrl .= "&";
		    }
		    $newUrl .= $param . "=" . $value;
		    $i++;
	    }
	    $action->controller->redirect($newUrl);
    }
}