<?php

namespace backend\controllers;

use Yii;
use common\models\EventLog;
use common\models\EventExpense;
use common\models\EventExpenseSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventExpenseController implements the CRUD actions for EventExpense model.
 */
class EventExpenseController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all EventExpense models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null)
    {
        $searchModel = new EventExpenseSearch();
        $date = new \DateTime();
        if($y==null)
        {
            $y = $date->format('Y');
        }
        if ($m==null)
        {
            $m = 0;
        }
        

        $params = Yii::$app->request->queryParams;
        $searchModel->year = $y;
        $searchModel->month = $m;           


        $dataProvider = $searchModel->search($params);
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date1 = clone $date;
        $date2 = clone $date;
        $date1->sub($dateInterval);
        $date2->add($dateInterval);
        $prev = [
            'y'=>$date1->format('Y'),
            'm'=>$date1->format('n'),
        ];
        $next = [
            'y'=>$date2->format('Y'),
            'm'=>$date2->format('n'),
        ];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'y'=>$y,
            'm'=>$m,
            'prev'=>$prev,
            'next'=>$next
        ]);
    }

    /**
     * Displays a single EventExpense model.
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
     * Creates a new EventExpense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new EventExpense();
        $model->event_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
                        $eventlog = new EventLog;
                        $eventlog->event_id = $id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Do eventu dodano koszt").": ".$model->name.".";
                        $eventlog->save();
            return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventExpense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadSections();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventExpense model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('danger', Yii::t('app', 'Usunięto'));
        return $this->redirect(['event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
    }

    /**
     * Finds the EventExpense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventExpense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventExpense::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
