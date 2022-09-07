<?php


namespace backend\modules\api\controllers;


use common\models\Rent;
use Yii;
use DateTime;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use backend\modules\permission\models\BasePermission;
use yii\helpers\ArrayHelper;

class RentController extends BaseController {
	public $modelClass = 'common\models\Rent';



    public function actionGet($id)
    {
        $user = Yii::$app->user;
            $event = Rent::findOne($id);
            if ($event)
            {
                    $tmpEvent = $event->toArray();
                    $tmpEvent['task_status'] = $event->getTaskStatus();
                    $tmpEvent['gears'] = $event->getAssignedGearsArray();
                    return ['rent'=>$tmpEvent];
            }else{
                throw new BadRequestHttpException(Yii::t('app', 'Brak wydarzenia'));
            }
            
        throw new BadRequestHttpException(Yii::t('app', 'Something wrong'));
    }


}