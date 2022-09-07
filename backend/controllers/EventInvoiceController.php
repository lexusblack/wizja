<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\EventLog;
use common\models\EventInvoice;
use common\models\EventInvoiceSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventInvoiceController implements the CRUD actions for EventInvoice model.
 */
class EventInvoiceController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['eventsEventEditEyeFinanceAttachmentsDelete']
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['eventsEventEditEyeFinanceAddInvoice']
                ],
                [
                    'allow' => true,
                    'actions' => ['download'],
                    'roles' => ['eventsEventEditEyeFinanceAttachments']
                ]
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/event-invoice'

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all EventInvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventInvoice model.
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
     * Creates a new EventInvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new EventInvoice();
        $model->event_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
                        $eventlog = new EventLog;
                        $eventlog->event_id = $id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Do eventu dodano fakturę.");
                        $eventlog->save();
            return $this->redirect(['/event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventInvoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->redirect(['/event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventInvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano'));
        return $this->redirect(['/event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);

    }

    /**
     * Finds the EventInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }
}
