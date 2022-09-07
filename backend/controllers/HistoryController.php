<?php

namespace backend\controllers;

use Yii;
use common\models\Attachment;
use common\models\AttachmentSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttachmentController implements the CRUD actions for Attachment model.
 */
class HistoryController extends Controller
{

    public function actions()
    {
        $actions = [
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all Attachment models.
     * @return mixed
     */
    public function actionGearItem($id)
    {
        $searchModel = new Gear();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


}
