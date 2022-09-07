<?php

namespace backend\controllers;

use backend\modules\permission\models\BasePermission;
use common\components\filters\AccessControl;
use common\models\Notification;
use Yii;
use common\models\Task;
use common\models\TaskDone;
use common\models\TaskNote;
use common\models\TaskNotification;
use common\models\TaskCategory;
use common\models\TaskSearch;
use common\models\EventSearch;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'done', 'update-status', 'order', 'order-cat', 'small-view', 'small-view-table', 'ordered', 'all', 'events', 'add-note', 'add-notification', 'delete-notification', 'edit-notification', 'edit-users', 'add-for-event', 'save-calendar-date', 'change-status-modal', 'edit-name'],
                        'roles' => ['menuTasks'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['set-done'],
                        'roles' => ['menuTasksAccept'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'create-cat', 'cat-update', 'cat-delete', 'create-subtask'],
                        'roles' => ['menuTasksAdd'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['menuTasksView']

                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            if (Yii::$app->user->can('menuTasksDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                                return true;
                            }
                            return $this->isMine('menuTasksDelete');
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            if (Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL])) {
                                return true;
                            }
                            return $this->isMine('menuTasksEdit');
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionChangeStatusModal($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())&&$model->save())
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $event = $model;
            $note = new TaskNote;
            $note->task_id = $id;
            $note->user_id = Yii::$app->user->id;
            $note->text = Yii::t('app', 'Zmiana statusu zadania na: ');
            if ($model->status==10)
            {
                $note->text .=Yii::t('app', 'wykonane');
                $model->sendDoneNotifications(Yii::$app->user->id);
                if ($model->cyclic_type)
                {
                    $clone = $model->copyMe();
                    $clone->status = 0;
                    $clone->updateCyclicDate();
                }
                    }
            else
                $note->text .=Yii::t('app', 'niewykonane');        
            $note->save();
            $success = $event->prepareForCalendar();
                return $success;
                exit;
        }
        return $this->renderAjax('change-status-modal', [
            'model'=>$model
        ]);
    }

    public function actionSaveCalendarDate()
    {
        $event = Task::findOne(Yii::$app->request->post("id"));
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($event)
        {
            if (Yii::$app->request->post("no-date"))
            {
                $event->from= null;
                $event->datetime= null;
                $event->save();
                $success = $event->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;
            }

            $date = Yii::$app->request->post("date_start");
            $dateArr = explode( "-", $date);

            if (Yii::$app->request->post("whole")==1)
            {
                $hourArr = explode( ":", Yii::$app->request->post("hour_start"));
                $d=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $event->from= date("Y-m-d H:i:s", $d);
                $d2=mktime($hourArr[0]+1, $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]); 
                $event->datetime = date("Y-m-d H:i:s", $d2);
                $event->save();
                $success = $event->prepareForCalendar();
                return $success;
                exit;
            }else{
                
                $hourArr = explode( ":", Yii::$app->request->post("hour_start"));
                $d=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $date = Yii::$app->request->post("date_end");
                $dateArr = explode( "-", $date);
                $hourArr = explode( ":", Yii::$app->request->post("hour_end"));
                $d2=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $event->from = date("Y-m-d H:i:s", $d);
                $event->datetime= date("Y-m-d H:i:s", $d2);
                $event->save();
                $success = $event->prepareForCalendar();
                return $success;
                exit;
            }
        }
        exit;
    }

    private function isMine($text) {
        $task = $this->findModel(Yii::$app->request->get('id'));
        if (Yii::$app->user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            if ($task->creator_id == Yii::$app->user->id) {
                return true;
            }else{
                if (isset($task->event_id))
                {
                    if ((Yii::$app->user->id==$task->event->manager_id)||(Yii::$app->user->id==$task->event->creator_id))
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function actionOrder($event_id=null, $cat=null)
    {
        $i = 1;
        $data = json_decode(Yii::$app->request->post('data'));
        //echo var_dump($data);
        //exit;
        foreach ( $data as $value) {
            $id = substr($value->id, 5);
            $model = $this->findModel($id);
            $model->order = $i;
            $model->task_id = null;
            $model->type = 1;
            if ($event_id)
            {
                $model->event_id = $event_id;
            }
            if ($cat)
                $model->task_category_id = $cat;
            $model->save();
            $i++;
            if (isset($value->children))
            {
                foreach ($value->children as $child)
                {
                    $chil_id = substr($child->id, 5);
                    $model2 = $this->findModel($chil_id);
                    $model2->order = $i;
                    $model2->task_id = $id;
                    $model2->type = 2;
                    if ($model->getEventProdukcja())
                    {
                        $model2->event_id = $model->getEventProdukcja()->id;
                        $model2->type = 1;
                        $model2->task_category_id = null;
                    }
                    $model2->save();
                    $i++;
                }
            }
        }
        exit;
    }

    public function actionOrderCat()
    {
        $i = 1;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = TaskCategory::findOne($value);
            $model->order = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionCreateSubtask($id){
        $model = new Task();
        $model->task_id = $id;
        $model->event_id = $model->task->event_id;
        $model->rent_id = $model->task->rent_id;
        $model->task_category_id = $model->task->task_category_id;
        $model->customer_id = $model->task->customer_id;
        $model->project_id = $model->task->project_id;
        $model->gear_id = $model->task->gear_id;
        $model->creator_id = Yii::$app->user->id;
        $model->type = 2;
        
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if ($model->event_id)
                \common\models\Note::createNote(2, 'eventTaskAdded', $model, $model->event_id);
            else
                \common\models\Note::createNote(4, 'taskAdded', $model, $model->id);
                $response= Task::find()->where(['id'=>$model->id])->asArray()->one();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;                
        }else{
            return $this->renderAjax('_smallform', [
                        'model' => $model
            ]);
        }

    }

    public function actionCreateCat($event_id=null, $rent_id=null, $project_id=null)
    {
        $model = new TaskCategory();
        if ($event_id)
        {
        $model->event_id = $event_id;
        $model->order = TaskCategory::find()->where(['event_id'=>$event_id])->count()+1;            
    }else{
        if ($rent_id)
        {
            $model->rent_id = $rent_id;
            $model->order = TaskCategory::find()->where(['rent_id'=>$rent_id])->count()+1;           
        }else{
            if ($project_id)
            {
                $model->project_id = $project_id;
                $model->order = TaskCategory::find()->where(['project_id'=>$rent_id])->count()+1;               
            }
        }
        
    }

        $model->color = "#273a4a";

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = TaskCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formcat', [
                        'model' => $model
            ]);
        }
    }

    public function actionAddNote($id)
    {
        $model = new TaskNote();
        $model->task_id = $id;
        $model->user_id = Yii::$app->user->id;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post())) {
            $model->save();            
            $response = $model;
            return $response;
        }
        return "";
    }

    public function actionCatUpdate($id)
    {
        $model = TaskCategory::find()->where(['id'=>$id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = TaskCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formcat', [
                        'model' => $model
            ]);
        }
    }

    public function actionCatDelete($id)
    {
        $model = TaskCategory::find()->where(['id'=>$id])->one();
        $response = $model;
        $model->delete();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex($id=null)
    {
        $searchModel = new TaskSearch();
        $searchModel->my_status = 2;
        $searchModel->task_datetime = 1;
        $dataProvider = $searchModel->searchMine(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menu'=>1,
            'id'=>$id
        ]);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionOrdered()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->searchOrdered(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menu'=>2,
            'id'=>null
        ]);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionAll()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->searchAll(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menu'=>4,
            'id'=>null
        ]);
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('newview', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionSmallView($id, $content=false, $type=false)
    {
        if ($type=="customer")
        {
            return $this->renderAjax('smallviewcustomer', [
            'task' => $this->findModel($id),
            'content' => $content
        ]);
        }else{
            return $this->renderAjax('smallview', [
            'task' => $this->findModel($id),
            'content' => $content
        ]);
        }
        
    }

    public function actionSmallViewTable($id)
    {
        return $this->renderAjax('smallviewtable', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdateStatus($id, $status)
    {
        $model = $this->findModel($id);
        $model->status=$status;
        $model->comment = Yii::$app->request->post('comment');
        $model->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;

    }

    public function actionDone($id)
    {
        $model = $this->findModel($id);
        if ($model->only_one)
        {
            if ($model->status==10)
            {
                $model->status=null;
            }else{
                $model->status=10;
            }
            $model->save();  
            $note = new TaskNote;
            $note->task_id = $id;
            $note->user_id = Yii::$app->user->id;
            $note->text = Yii::t('app', 'Zmiana statusu zadania na: ');
            if ($model->status==10)
            {
                $note->text .=Yii::t('app', 'wykonane');
                $model->sendDoneNotifications(Yii::$app->user->id);
                if ($model->cyclic_type)
                {
                    $clone = $model->copyMe();
                    $clone->status = 0;
                    $clone->updateCyclicDate();
                }
                    }
            else
                $note->text .=Yii::t('app', 'niewykonane');        
            $note->save();
        }else{
            $done = TaskDone::find()->where(['task_id'=>$id])->andWhere(['user_id'=>Yii::$app->user->id])->one();
            if ($done){
                $done->delete();
                $note = new TaskNote;
                $note->task_id = $id;
                $note->user_id = Yii::$app->user->id;
                $note->text = Yii::t('app', 'Zmiana swojego statusu zadania na: ');
                $note->text .=Yii::t('app', 'niewykonane');        
                $note->save();
            }else{
                $done = new TaskDone;
                $done->task_id = $id;
                $done->user_id = Yii::$app->user->id;
                $done->status = 10;
                $done->save();
                $note = new TaskNote;
                $note->task_id = $id;
                $note->user_id = Yii::$app->user->id;
                $note->text = Yii::t('app', 'Zmiana swojego statusu zadania na: ');
                $note->text .=Yii::t('app', 'wykonane');  

                $note->save();
                $model->sendDoneNotificationsUser($note->user_id);
            }
            $model->status = $model->checkStatus();
            $model->save();
                if (($model->cyclic_type)&&($model->status==10))
                {
                    $clone = $model->copyMe();
                    $clone->status = 0;
                    $clone->updateCyclicDate();
                }

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;

    }

    public function actionSetDone($id)
    {
        $model = $this->findModel($id);
        $model->status=10;
        $model->save();  
        if ($model->cyclic_type)
        {
            $clone = $model->copyMe();
            $clone->status = 0;
            $clone->updateCyclicDate();
        }
        $model->sendDoneNotifications(Yii::$app->user->id);
            $note = new TaskNote;
            $note->task_id = $id;
            $note->user_id = Yii::$app->user->id;
            $note->text = Yii::t('app', 'Zmiana statusu zadania na: ');
            $note->text .=Yii::t('app', 'wykonane');       
            $note->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;

    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($category_id=null, $event_id=null, $customer_id=null, $rent_id=null, $project_id=null, $gear_id=null, $user_id=null)
    {
        $model = new Task();
        $model->event_id = $event_id;
        $model->rent_id = $rent_id;
        $model->task_category_id = $category_id;
        $model->customer_id = $customer_id;
        $model->project_id = $project_id;
        $model->gear_id = $gear_id;
        $model->user_id = $user_id;
        $model->creator_id = Yii::$app->user->id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $model->linkObjects();
            $model->linkTeams();
            $model->sendCreateNotifications();
            $model->createChangeUsersNote([]);
            if ($model->event_id)
                \common\models\Note::createNote(2, 'eventTaskAdded', $model, $model->event_id);
            else
                \common\models\Note::createNote(4, 'taskAdded', $model, $model->id);
            if (!Yii::$app->request->isAjax) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['all']);
            }else{
                $response= Task::find()->where(['id'=>$model->id])->asArray()->one();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;                
            }
        }
        else
        {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                        'model' => $model,
                        'edit_all'=>true,
                        'ajax'=>true
            ]);
            }else{
                return $this->render('create', [
                'model' => $model,
                ]);               
            }

        }
    }

    public function actionEvents()
    {
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();

        return $this->render('events', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menu'=>3
        ]);        
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionEditName($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save())
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
                return $model;
        }else{
            return $this->renderAjax('edit-name', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        $users = $model->users;
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->linkObjects();
            $model->linkTeams();

            $model->save();
            $model->createChangeUsersNote($users);
            if (!Yii::$app->request->isAjax) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['index']);
            }else{
                $response= Task::find()->where(['id'=>$model->id])->asArray()->one();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;                
            }
        }
        else
        {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                        'model' => $model,
                        'edit_all'=>true,
                        'ajax'=>true
            ]);
            }else{
                return $this->render('update', [
                'model' => $model,
                ]);               
            }
        }
    }

public function actionEditUsers($id, $cal=false)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        $users = $model->users;
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->linkObjects();
            $model->createChangeUsersNote($users);
            if ($cal)
            {
                $response= Task::find()->where(['id'=>$model->id])->one();
                $response = $response->prepareForCalendar();
            }else{
                $response= Task::find()->where(['id'=>$model->id])->asArray()->one();
            }
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;                
        }
        else
        {
                return $this->renderAjax('_edit_users', [
                        'model' => $model,
                        'edit_all'=>true,
                        'ajax'=>true,
                        'cal'=>$cal
            ]);
        }
    }

public function actionAddForEvent($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
            $response= Task::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;                
        }
        else
        {
                return $this->renderAjax('_edit_events', [
                        'model' => $model,
                        'ajax'=>true
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        if (Yii::$app->request->post('ajax'))
            exit;
        return $this->goBack();
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionAddNotification($id)
    {
        $task = $this->findModel($id);
        $model = new TaskNotification();
        $model->task_id = $id;
        $model->time = 1;
        $model->time_type = 3;
        $model->email = 1;
        $model->text = Yii::t('app', 'Masz zadanie do wykonania').": ".$task->title;
        $model->save();
        exit;
    }

    public function actionEditNotification($id)
    {
        $model = TaskNotification::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }
        exit;
    }

    public function actionDeleteNotification($id)
    {
        $model = TaskNotification::findOne($id);
        $model->delete();
        exit;


    }
}
