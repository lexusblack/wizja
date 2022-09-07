<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\Ride;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class RideController extends BaseController {

    public $modelClass = '\common\models\Ride';


    public function actionAdd()
    {
        $ride = new Ride;
        $ride->vehicle_id = Yii::$app->request->post('vehicle_id');
        $ride->event_id = Yii::$app->request->post('event_id');
        $ride->km_start = Yii::$app->request->post('km_start');
        $ride->start_place = Yii::$app->request->post('start_place');
        $ride->end_place = Yii::$app->request->post('end_place');
        $ride->start = Yii::$app->request->post('start_datetime');
        $ride->user_id = Yii::$app->user->identity->id;
        if ($ride->save())
        {
                        return [
                            'name' => Yii::t('app', 'Przejazd'),
                            'message' => Yii::t('app', 'Dodano przejazd'),
                            'code' => 0,
                            'status' => 200,
                            'id' => $ride->id
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

    public function actionEdit()
    {
        $ride = Ride::findOne(Yii::$app->request->post('id'));
        $ride->km_end = Yii::$app->request->post('km_end');
        $ride->end = Yii::$app->request->post('end_datetime');
        if ($ride->save())
        {
                        return [
                            'name' => Yii::t('app', 'Przejazd'),
                            'message' => Yii::t('app', 'Zaktualizowano przejazd'),
                            'code' => 0,
                            'status' => 200,
                            'id' => $ride->id
                        ];
        }else{
            echo var_dump($ride);
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

}