<?php

namespace backend\controllers;

use Yii;
use common\models\EventGearItem;
use common\models\EventGearItemSearch;
use backend\components\Controller;
use common\models\Event;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventGearItemController implements the CRUD actions for EventGearItem model.
 */
class EventGearItemController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all EventGearItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventGearItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventGearItem model.
     * @param integer $event_id
     * @param integer $gear_item_id
     * @return mixed
     */
    public function actionView($event_id, $gear_item_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($event_id, $gear_item_id),
        ]);
    }

    /**
     * Creates a new EventGearItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($eventId)
    {
        $model = new EventGearItem();
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
     * Updates an existing EventGearItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $event_id
     * @param integer $gear_item_id
     * @return mixed
     */
    public function actionUpdate($event_id, $gear_item_id)
    {
        $model = $this->findModel($event_id, $gear_item_id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'event_id' => $model->event_id, 'gear_item_id' => $model->gear_item_id]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventGearItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $event_id
     * @param integer $gear_item_id
     * @return mixed
     */
    public function actionDelete($event_id, $gear_item_id)
    {
        $this->findModel($event_id, $gear_item_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventGearItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $event_id
     * @param integer $gear_item_id
     * @return EventGearItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($event_id, $gear_item_id)
    {
        if (($model = EventGearItem::findOne(['event_id' => $event_id, 'gear_item_id' => $gear_item_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    protected function findEvent($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
