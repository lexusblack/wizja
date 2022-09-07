<?php

namespace backend\controllers;

use Yii;
use common\models\EventMessage;
use common\models\EventMessageSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventMessageController implements the CRUD actions for EventMessage model.
 */
class EventMessageController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>\common\components\filters\AccessControl::className(),
            'baseName' => 'eventTabNotification',
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['send'],
                    'roles' => ['eventsEventEditEyeNotifications'],
                ],
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all EventMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventMessage model.
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
     * Creates a new EventMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventMessage();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionSend($id)
    {
        $model = new EventMessage();
        $model->event_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->send())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Wysłano!'));
        }
        else
        {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Nie wysłano!'));
        }
        return $this->redirect(['/event/view', 'id' => $model->event_id, '#'=>'tab-message']);
    }
}
