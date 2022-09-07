<?php


namespace backend\modules\api\controllers;


use common\models\Meeting;
use Yii;
use DateTime;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use backend\modules\permission\models\BasePermission;
use yii\helpers\ArrayHelper;

class MeetingController extends BaseController {
	public $modelClass = 'common\models\Meeting';


    public function actionAdd()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $start = Yii::$app->request->post('start');
            $end = Yii::$app->request->post('end');
            $name = Yii::$app->request->post('name');
            $description = Yii::$app->request->post('description');
            $location = Yii::$app->request->post('location');
            $customer_id = Yii::$app->request->post('customer_id');
            $contact_id = Yii::$app->request->post('contact_id');
            $users = json_decode(Yii::$app->request->post('users'));
            $model = new Meeting(['name'=>$name, 'start_time'=>$start, 'end_time'=>$end, 'description'=>$description, 'customer_id'=>$customer_id, 'contact_id'=>$contact_id, 'location'=>$location, 'userIds'=>$users]);


                if ($model->save())
                {
                        $model->linkObjects();
                        \common\models\Note::createNote(4, 'meetingAdded', $model, $model->customer_id);
                        return [
                            'name' => Yii::t('app', 'Spotkanie'),
                            'message' => Yii::t('app', 'Dodano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        throw new MethodNotAllowedHttpException();
    }

}