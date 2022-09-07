<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Personal;
use common\models\PersonalSearch;
use backend\components\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * PersonalController implements the CRUD actions for Personal model.
 */
class PersonalController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'baseName' => $this->id,
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index', 'view', 'create', 'delete', 'update', 'delete-sms', 'delete-mail'],
                    'roles' => ['eventsMeetingsPrivate'],
                ]
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all Personal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PersonalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['user_id' => Yii::$app->user->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Personal model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Personal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null)
    {
        $model = new Personal();
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $model->start_time = date($start.' 00:00:00');
        $model->end_time = date($start.' 23:59:59');
        $model->user_id = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Personal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->notificationSms) {
            $model->notificationSms->delete();
        }
        if ($model->notificationMail) {
            $model->notificationMail->delete();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Personal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleteNotificationSms();
        $model->deleteNotificationMail();
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Personal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Personal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Personal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDeleteSms($id) {
        if (Yii::$app->request->isPost) {
            $this->findModel($id)->deleteNotificationSms();
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }

    public function actionDeleteMail($id) {
        if (Yii::$app->request->isPost) {
            $this->findModel($id)->deleteNotificationMail();
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }
}
