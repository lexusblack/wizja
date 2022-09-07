<?php

namespace backend\controllers;

use Yii;
use common\models\EventUserAddon;
use common\models\EventLog;
use common\models\EventUserAddonSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventUserAddonController implements the CRUD actions for EventUserAddon model.
 */
class EventUserAddonController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all EventUserAddon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventUserAddonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventUserAddon model.
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
     * Creates a new EventUserAddon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $userId = null, $admin=false)
    {
        $model = new EventUserAddon();
        $model->event_id = $id;
        $ev = \common\models\Event::findOne($id);
        $model->start_time = $ev->getTimeStart();
        $model->end_time = $ev->getTimeEnd();
        if ($userId)
            $model->user_id = $userId;
        else{
            if (!$admin)
                $model->user_id = Yii::$app->user->id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
                        $eventlog = new EventLog;
                        $eventlog->event_id = $id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Do eventu dodano koszt")." ".$model->name.".";
                        $eventlog->save();
                if (Yii::$app->request->isAjax) {
                    return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-working-time']);
                } else {
                    return $this->goBack();
                }
            
        }
        else
        {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('create', [
                        'model' => $model,
                    ]);
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
        }
    }

    /**
     * Updates an existing EventUserAddon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->goBack();
            //return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-working-time']);
        }
        else
        {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('update', [
                        'model' => $model,
                    ]);
                } else {
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
        }
    }

    /**
     * Deletes an existing EventUserAddon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Usunięto'));
        return $this->goBack();
        //return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-working-time']);
    }

    /**
     * Finds the EventUserAddon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventUserAddon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventUserAddon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
