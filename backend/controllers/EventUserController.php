<?php

namespace backend\controllers;

use Yii;
use common\models\EventUser;
use common\models\EventUserSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use common\components\filters\AccessControl;

/**
 * EventUserController implements the CRUD actions for EventUser model.
 */
class EventUserController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['confirm'],
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }
    public $enableCsrfValidation = false;


    public function actionConfirm($user_id, $event_id, $id)
    {
        $model = $this->findModel($event_id, $user_id);
        if ($model)
        {
            $hash = $model->getHash();
            if ($hash==$id)
            {
                $model->confirm = 1;
                $model->save();
                $this->layout = "empty";
                return $this->render('confirm', [
                    'model' => $model,]);
            }else{
                exit;
            
            }
        }else{
            exit;
        }
    }

    /**
     * Displays a single EventUser model.
     * @param integer $event_id
     * @param integer $user_id
     * @return mixed
     */
    public function actionView($event_id, $user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($event_id, $user_id),
        ]);
    }

    /**
     * Creates a new EventUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($eventId)
    {
        $model = new EventUser();

        $event = $this->findEvent($eventId);
        $model->event_id = $event->id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(Url::previous('event'));
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $event_id
     * @param integer $user_id
     * @return mixed
     */
    public function actionUpdate($event_id, $user_id)
    {
        $model = $this->findModel($event_id, $user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'event_id' => $model->event_id, 'user_id' => $model->user_id]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $event_id
     * @param integer $user_id
     * @return mixed
     */
    public function actionDelete($event_id, $user_id, $reload=true)
    {
        $this->findModel($event_id, $user_id)->delete();
        if($reload == true){
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the EventUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $event_id
     * @param integer $user_id
     * @return EventUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($event_id, $user_id)
    {
        if (($model = EventUser::findOne(['event_id' => $event_id, 'user_id' => $user_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }


}
