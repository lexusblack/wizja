<?php

namespace backend\controllers;

use Yii;
use common\models\EventReport;
use common\models\EventReportSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EventReportController implements the CRUD actions for EventReport model.
 */
class EventReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['calculate'],
                        'allow' => true,
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    public function actionCalculate($redirect=false, $offset=0, $event_id=null)
    {
       $date = date("Y-m-d", time() - 1200 * 60 * 24);;
        $ids = \common\helpers\ArrayHelper::map(\common\models\EventLog::find()->where(['>', 'create_time', $date])->asArray()->all(), 'event_id', 'event_id');
        $offer_ids = \common\helpers\ArrayHelper::map(\common\models\OfferLog::find()->where(['>', 'create_time', $date])->asArray()->all(), 'offer_id', 'offer_id'); 
        $ids2 = \common\helpers\ArrayHelper::map(\common\models\Offer::find()->where(['id'=>$offer_ids])->asArray()->all(), 'event_id', 'event_id');
        $ids3 = \common\helpers\ArrayHelper::map(\common\models\Note::find()->where(['>', 'datetime', $date])->asArray()->all(), 'event_id', 'event_id');
        $events = \common\models\Event::find()->where(['id'=>$ids])->orWhere(['id'=>$ids2])->orWhere(['id'=>$ids3])->limit(50)->offset($offset)->orderBy(['id'=>SORT_DESC])->all();
        //$events = \common\models\Event::find()->where(['>', 'event_start', '2020-07-01'])->limit(50)->offset($offset)->orderBy(['id'=>SORT_DESC])->all(); 
        date_default_timezone_set(Yii::$app->params['timeZone']);
        if ($event_id)
            $events = \common\models\Event::find()->where(['id'=>$event_id])->all();
        foreach ($events as $event)
        {
            \common\models\EventReportProvisions::deleteAll(['event_id'=>$event->id]);
            \common\models\EventReport::deleteAll(['event_id'=>$event->id]);

            $report = new EventReport();

            $report->create_time = date('Y-m-d H:i:s');
            $report->event_id = $event->id;
            $report->name = $event->name;
            $report->customer_id = $event->customer_id;
            $report->manager_id = $event->manager_id;
            $report->event_start = $event->getTimeStart();
            $report->event_end = $event->getTimeEnd();
            $report->paying_date = $event->paying_date;
            $report->code = $event->code;
            if ($event->location)
                $report->location = $event->location->name.", ".$event->location->city;
            else
                $report->location = $event->address;
            $report->event_model_id =$event->event_type;
            $report->event_type_id =  $event->type;
            $report->status = $event->status;
            $report->total_value = $event->getEventValueAll()[Yii::t('app', 'Suma')];
            $report->total_cost = $event->getEventCosts()[Yii::t('app', 'Suma')];
            $provisions = 0;
            foreach (\common\models\ProvisionGroup::find()->all() as $gp)
            {
                    $v = \common\models\EventProvisionValue::find()->where(['event_id'=>$event->id, 'provision_group_id'=>$gp->id, 'section'=>Yii::t('app', 'Suma')])->asArray()->one();
                    if ($v)
                    {
                        $provisions += $v['value'];
                        $erp = new \common\models\EventReportProvisions();
                        $erp->event_id = $event->id;
                        $erp->provision_group_id = $gp->id;
                        $erp->value = $v['value'];
                        $erp->save();

                    }
            }
            $report->total_provision = $provisions;
            $report->total_predicted_cost = $event->getEventPredictedCost()[Yii::t('app', 'Suma')];
            $report->total_predicted_provision = $event->getEventPredictedProvisions()[Yii::t('app', 'Suma')];
            $report->paid = $event->getEventPaid();
            $report->fv_total = $event->getEventFV();
            $report->prepaid = $event->getEventPMcost();
            $report->save();

        }
        if ($redirect)
        {
            return $this->redirect(['index']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($events)
        {
            return ['success'=>1];
        }else{
            return ['success'=>0];
        }
        
        //return false;
        //exit;
    }

    /**
     * Lists all EventReport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventReportSearch();
        $params = Yii::$app->request->queryParams;

        if (count($params)<1) {
          $params = Yii::$app->session['eventreportparams'];
          $params['EventReportSearch']['paying_date']=[date("Y-m")."-01"];
          if (count(Yii::$app->session['eventreportparams'])<1)
          {
            $params['EventReportSearch']['paying_date']=[date("Y-m")."-01"];
          }
          } else {
            Yii::$app->session['eventreportparams'] = $params;
            
        }
        $dataProvider = $searchModel->search($params);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventReport model.
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
     * Creates a new EventReport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventReport();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventReport model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventReport model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the EventReport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventReport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventReport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
