<?php

namespace backend\controllers;

use Yii;
use common\models\EventLog;
use common\models\EventUserWorkingTime;
use common\models\EventUserAddon;
use common\models\EventUserAllowance;
use common\models\EventUserWorkingTimeSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventUserWorkingTimeController implements the CRUD actions for EventUserWorkingTime model.
 */
class EventUserWorkingTimeController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all EventUserWorkingTime models.
     * @return mixed
     */
    public function actionGenerateRaport($start, $end)
    {
        $times = EventUserWorkingTime::find()->where(['>', 'start_time', $start])->andWhere(['<', 'start_time', $end])->all();
        $data = [];
        
       $data[] = [Yii::t('app', 'Nazwisko i imię'), Yii::t('app', 'Wydarzenie'), Yii::t('app', 'Kod'), Yii::t('app', 'Dział'), Yii::t('app', 'Rola'), Yii::t('app', 'Okres'), Yii::t('app', 'Czas'), Yii::t('app', 'Początek'), Yii::t('app', 'Koniec')];
       $okres = [1=>Yii::t('app', 'Pakowanie'), 2=>Yii::t('app', 'Montaż'), 3=>Yii::t('app', 'Event'), 4=>Yii::t('app', 'Demontaż')];
        foreach ($times as $time)
        {
            if ($time->role)
            {
                $role = $time->role->name;
            }else{
                $role = "-";
            }
            if ($time->department)
            {
                $d = $time->department->name;
            }else{
                $d = "-";
            }
            if ($time->type)
            {
                $o = $okres[$time->type];
            }else{
                $o = "-";
            }
            $hours = floor($time->duration/3600).":";
            $minutes = floor(($time->duration-floor($time->duration/3600)*3600)/60);
            if ($minutes<10)
                $minutes = "0".$minutes;
            $hours .=$minutes; 
            $data[] = [$time->user->displayLabel, $time->event->name, $time->event->code, $d, $role, $o, $hours, $time->start_time, $time->end_time];
        }

        $times = EventUserAddon::find()->where(['>', 'start_time', $start])->andWhere(['<', 'start_time', $end])->all();
        $data2 = [];
        
       $data2[] = [Yii::t('app', 'Nazwisko i imię'), Yii::t('app', 'Wydarzenie'), Yii::t('app', 'Kod'),  Yii::t('app', 'Nazwa'),  Yii::t('app', 'Opis'),  Yii::t('app', 'Kwota'), Yii::t('app', 'Początek'), Yii::t('app', 'Koniec')];
        foreach ($times as $time)
        {

            $data2[] = [$time->user->displayLabel, $time->event->name, $time->event->code, $time->name, $time->info, $time->amount, $time->start_time, $time->end_time];
        }

        $times = EventUserAllowance::find()->where(['>', 'start_time', $start])->andWhere(['<', 'start_time', $end])->all();
        $data3 = [];
        
       $data3[] = [Yii::t('app', 'Nazwisko i imię'), Yii::t('app', 'Wydarzenie'), Yii::t('app', 'Kod'), Yii::t('app', 'Kwota'), Yii::t('app', 'Początek'), Yii::t('app', 'Koniec')];
        foreach ($times as $time)
        {

            $data3[] = [$time->user->displayLabel, $time->event->name, $time->event->code, $time->amount, $time->start_time, $time->end_time];
        }

        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                "Raport godziny" => [   // Name of the excel sheet
                    'data' => $data,
                    'titles' => false
                ],
                "Raport koszty" => [   // Name of the excel sheet
                    'data' => $data2,
                    'titles' => false
                ],
                "Raport diety" => [   // Name of the excel sheet
                    'data' => $data3,
                    'titles' => false
                ],
            ]
        ]);

        foreach(range('A','H') as $columnID) {
            $file->getWorkbook()->getSheet(0)->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $file->send('Raport'.$start.'.xlsx');
        //echo var_dump($data);
        exit;
    }

    public function actionIndex()
    {
        $searchModel = new EventUserWorkingTimeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventUserWorkingTime model.
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
     * Creates a new EventUserWorkingTime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $user_id=null, $cal=false, $task_id=null, $admin=false)
    {
        if ($task_id)
        {
            $task = \common\models\Task::findOne($task_id);
            $id = $task->event_id;
        }
        if (!$user_id)
            if (!$admin)
            {
                $user_id = Yii::$app->user->id;
            }else{
                $user_id = null;
            }
            
        $workingTime = new EventUserWorkingTime([
            'user_id'=>$user_id,
            'event_id'=>$id,
        ]);
        if ($task_id)
        {
            $workingTime->task_id = $task_id;
        }
        $workingTime->loadLinkedObjects();
        $post = Yii::$app->request->post();
        if ($workingTime->load($post) && $workingTime->saveAndLink(true))
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Do eventu dodano godziny pracy.");
            $eventlog->save();
            $workingTime->event->updateParentExpense();
            if (!$cal)
                return $this->goBack();
            else
                exit;
        } else {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('create', [
                        'model' => $workingTime,
                        'ajax' =>$cal
                    ]);
                } else {
                    return $this->render('create', [
                        'model' => $workingTime,
                        'ajax' =>$cal
                    ]);
                }
        }
    }

    /**
     * Updates an existing EventUserWorkingTime model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->saveAndLink(true))
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            $model->event->updateParentExpense();
            return $this->goBack();
        } else {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('update', [
                        'model' => $model,
                        'ajax'=>true
                    ]);
                } else {
                    return $this->render('update', [
                        'model' => $model,
                        'ajax' => false
                    ]);
                }
        }
    }

    /**
     * Deletes an existing EventUserWorkingTime model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$redirect=true)
    {
        $model = $this->findModel($id);
        $model->delete();
        if($redirect){
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Skasowano!'));
            return $this->goBack();
        }
    }

    /**
     * Finds the EventUserWorkingTime model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventUserWorkingTime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventUserWorkingTime::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
