<?php


namespace backend\controllers;


use common\models\MobileQrScan;
use Yii;
use yii\web\Response;

class MobileScanController extends \yii\web\Controller {

	public function actionLastReadings($seconds) {
		date_default_timezone_set(Yii::$app->params['timeZone']);
		Yii::$app->response->format = Response::FORMAT_JSON;
		$gears = [];
		$date = new \DateTime();
		$date->sub(new \DateInterval("PT" . $seconds . "S"));

		$readings = MobileQrScan::find()->where([">", "created_at", $date->format("Y-m-d H:i:s")])->andWhere(['user_id' => Yii::$app->user->id])->all();
		foreach ($readings as $reading) {
			$gears[] = [$reading->type, $reading->gear_id];
		}

		return $gears;
	}
}