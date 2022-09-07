<?php


namespace backend\modules\api\controllers;


use common\models\Device;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnprocessableEntityHttpException;

class DeviceController extends BaseController {
	public $modelClass = 'common\models\Device';

	public function actionAdd() {
		if (Yii::$app->request->isPost) {
			$token = Yii::$app->request->post("new_token");
			$old_token = Yii::$app->request->post("old_token");

			if ($token != null) {

				if ($old_token != null) {
					$oldDevice = Device::find()->where(['token' => $old_token])->one();
					if ($oldDevice) {
						$oldDevice->delete();
					}
				}

				$device = new Device();
				$device->user_id = Yii::$app->user->id;
				$device->token = $token;
				if ($device->save()) {
					return ['status' => 200, 'message' => Yii::t('app', 'Dodano urządzenie')];
				}
				throw new UnprocessableEntityHttpException(Yii::t('app', "Coś poszło nie tak"));
			}
			throw new BadRequestHttpException(Yii::t('app', "Brak wymaganego parametru: new_token"));
		}
		throw new MethodNotAllowedHttpException();
	}

}