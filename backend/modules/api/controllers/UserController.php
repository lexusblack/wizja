<?php

namespace backend\modules\api\controllers;

use common\models\User;
use common\models\UserEventRole;
use common\models\Department;
use common\models\GearCategory;
use common\models\PurchaseType;
use common\models\Vehicle;
use common\models\Location;
use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;

class UserController extends BaseController {
    public $modelClass = 'common\models\User';

    public function actionUser($id) {
        throw new MethodNotAllowedHttpException();
    }

    public function actionPhoto($id) {
        if (Yii::$app->request->isGet) {
            if ($user = User::findOne($id)) {
                if ($user->photo) {
                    return  $this->getServerProtocol() . Yii::$app->getRequest()->serverName . $user->getPhotoUrl();
                }
                return [
                    'name' => Yii::t('app', 'Brak zdjęcia'),
                    'status' => 204,
                    'message' => Yii::t('app', 'Użytkownik nie posiada zdjęcia'),
                    'code' => 0
                ];
            }
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono użytkownika'));
        }
        throw new MethodNotAllowedHttpException();
    }

	public function actionLogin() {
    	if (Yii::$app->request->isPost) {
    		/** @var \common\models\User $user */
    		$user = Yii::$app->user->getIdentity();
    		return [
			    'name' => Yii::t('app', 'Logowanie'),
			    'message' => Yii::t('app', 'Prawidłowe dane'),
    		    'code' => 0,
			    'status' => 200,
			    'user' => [
			    	'id' => $user->id,
				    'photo' => $this->getServerProtocol() .  Yii::$app->getRequest()->serverName . $user->getPhotoUrl(),
				    'email' => $user->email,
				    'phone' => $user->phone,
				    'first_name' => $user->first_name,
				    'last_name' => $user->last_name,
			    ]
		    ];
	    }
	    throw new MethodNotAllowedHttpException();
	}

    public function actionGetAll() {
        if (Yii::$app->request->isPost) {
            $departments = ArrayHelper::map(Department::find()->asArray()->all(), 'id', 'name');
            $roles = ArrayHelper::map(UserEventRole::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
            $purchase_types = ArrayHelper::map(PurchaseType::find()->asArray()->all(), 'id', 'name');
            $service_statuts = \common\models\GearServiceStatut::getUserList();
            $categories = GearCategory::getTreeList();
            $vehicles = Vehicle::find()->where(['active'=>1])->asArray()->all();
            $sections = \common\models\EventExpense::getSectionList();
            $users = User::getList();
            $locations = Location::getList();
            $customers = \common\models\Customer::getAppList();
            $event_types = \common\models\Event::getTypeList();
            $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->id);
            $permsArray = [];
            foreach ($permissions as $key=>$p)
            {
                $permsArray[] = $key;
            }
            if ($vehicles)
                $veh = [];
            else    
                $veh = (object)[];
            foreach ($vehicles as $v)
            {
                $veh[$v['id']]= $v['name']." (".$v['registration_number'].")";
            }
            if (!$departments)
                $departments = (object)[];
            if (!$roles)
                $roles = (object)[];
            if (!$sections)
                $sections = (object)[];
            if (!$service_statuts)
                $service_statuts = (object)[];
            if (!$purchase_types)
                $purchase_types = (object)[];
            return ['locations'=>$locations, 'departments'=>$departments, 'roles'=>$roles, 'categories'=>$categories, 'vehicles'=>$veh, 'purchase_types'=>$purchase_types, 'sections'=>$sections, 'service_statuts'=>$service_statuts, 'users'=>$users, 'customers'=>$customers, 'event_types'=>$event_types, 'permissions'=>$permsArray];
        }
        throw new MethodNotAllowedHttpException();
    }

	private function getServerProtocol() {
    	if( isset($_SERVER['HTTPS']) ) {
    		return "https://";
	    }
	    return "http://";
	}
}
