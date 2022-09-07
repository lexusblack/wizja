<?php

namespace backend\controllers;

use Yii;
use common\models\EventLog;
use common\models\EventLogSearch;
use common\models\RentLogSearch;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventLogController implements the CRUD actions for EventLog model.
 */
class EventLogController extends Controller
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
        ];
    }

    /**
     * Lists all EventLog models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null)
    {
        $searchModel = new EventLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['eventlogsparams'];
          } 
        $date = new \DateTime();
        if($y==null)
        {
            if (isset($params['y']))
            {
                    $y = $params['y'];
            }else{
                    $y = $date->format('Y');
            }
            
        }
        if ($m==null)
        {
            if (isset($params['m']))
            {
                    $m = $params['m'];
            }else{
                    $m = $date->format('m');
            }
        }
        $searchModel->year = $y;
        $searchModel->month = $m; 
        if ($m)
            $date = \DateTime::createFromFormat('Yn', $y.$m);
        else
            $date =  \DateTime::createFromFormat('Y', $y);
        //$params = Yii::$app->request->queryParams;

        if (empty($params[$searchModel->formName()]['dateRange'])==true)
        {
            
            if ($m)
            {
                $searchModel->dateStart = $date->format('Y-m-01');
                $searchModel->dateEnd = $date->format('Y-m-t');
            }else{
                $searchModel->dateStart = $date->format('Y-01-01');
                $searchModel->dateEnd = $date->format('Y-12-31');                
            }

            $searchModel->year = $y;
            $searchModel->month = $m;
        }
        Yii::$app->session['eventlogsparams'] = $params;
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
            'next' => $next,
            'prev' => $prev,
            'date'=>$date,
            'y'=>$y,
            'm'=>$m
        ]);
    }
    public function actionRentIndex($y=null, $m=null)
    {
        $searchModel = new RentLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['eventlogsparams'];
          } 
        $date = new \DateTime();
        if($y==null)
        {
            if (isset($params['y']))
            {
                    $y = $params['y'];
            }else{
                    $y = $date->format('Y');
            }
            
        }
        if ($m==null)
        {
            if (isset($params['m']))
            {
                    $m = $params['m'];
            }else{
                    $m = $date->format('m');
            }
        }
        $searchModel->year = $y;
        $searchModel->month = $m; 
        if ($m)
            $date = \DateTime::createFromFormat('Yn', $y.$m);
        else
            $date =  \DateTime::createFromFormat('Y', $y);
        //$params = Yii::$app->request->queryParams;

        if (empty($params[$searchModel->formName()]['dateRange'])==true)
        {
            
            if ($m)
            {
                $searchModel->dateStart = $date->format('Y-m-01');
                $searchModel->dateEnd = $date->format('Y-m-t');
            }else{
                $searchModel->dateStart = $date->format('Y-01-01');
                $searchModel->dateEnd = $date->format('Y-12-31');                
            }

            $searchModel->year = $y;
            $searchModel->month = $m;
        }
        Yii::$app->session['eventlogsparams'] = $params;
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
        return $this->render('rent-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'next' => $next,
            'prev' => $prev,
            'date'=>$date,
            'y'=>$y,
            'm'=>$m
        ]);
    }
    /**
     * Displays a single EventLog model.
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
     * Creates a new EventLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventLog();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventLog model.
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
     * Deletes an existing EventLog model.
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
     * Finds the EventLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
