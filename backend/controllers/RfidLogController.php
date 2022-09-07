<?php

namespace backend\controllers;

use Yii;
use common\models\RfidLog;
use common\models\RfidLogSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * RfidLogController implements the CRUD actions for RfidLog model.
 */
class RfidLogController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'save' => ['post'],
                ],
            ],
        ];
    }
public function beforeAction($action) { $this->enableCsrfValidation = false; return parent::beforeAction($action); }
    /**
     * Lists all RfidLog models.
     * @return mixed
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionLoadNew($datetime, $id)
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $date = date('Y-m-d H:i:s');
        $logs = RfidLog::find()->where(['>', 'datetime', $datetime])->andWhere(['>', 'id', $id])->all();
        return ['datetime'=>$date, 'logs'=>$logs];
    }

    /**
     * Displays a single RfidLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RfidLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RfidLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RfidLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RfidLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSave()
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id= Yii::$app->request->post('Id');
        $content= Yii::$app->request->post('Content');
        $model = new RfidLog();
        $model->reader = $content['Reader'];
        $model->tag = $content['Tag'];
        $model->datetime = date('Y-m-d H:i:s');
        $model->save();
        return ['Status'=>'ok', 'Info'=>null]; 
    }

    
    /**
     * Finds the RfidLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RfidLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RfidLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
