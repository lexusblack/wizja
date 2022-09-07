<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\Purchase;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class PurchaseController extends BaseController {

    public $modelClass = '\common\models\Purchase';


    public function actionAdd()
    {
        $purchase = new Purchase;
        $purchase->description = Yii::$app->request->post('description');
        $purchase->sections = json_decode(Yii::$app->request->post('sections'));
        $purchase->eventIds = json_decode(Yii::$app->request->post('eventIds'));
        $purchase->price = Yii::$app->request->post('price');
        $purchase->purchase_type_id = Yii::$app->request->post('type');
        $purchase->user_id = Yii::$app->user->identity->id;
        $purchase->datetime = date('Y-m-d H:i:s');
        if ($purchase->save())
        {
                        $purchase->linkObjects();
                        return [
                            'name' => Yii::t('app', 'Zakup'),
                            'message' => Yii::t('app', 'Dodano zakup'),
                            'code' => 0,
                            'status' => 200,
                            'id' => $purchase->id
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

}