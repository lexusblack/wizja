<?php

namespace backend\controllers;

use Yii;
use common\models\Checklist;
use common\models\Todolist;
use common\models\ChecklistSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ChecklistController implements the CRUD actions for Checklist model.
 */
class ChecklistController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'clear', 'done', 'order', 'order-todolist', 'get-all', 'load-checklist', 'add-todolist', 'delete-todolist', 'delete-item', 'add-item', 'edit-item', 'clear-done', 'sort-done', 'add-task-todolist'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    /**
     * Lists all Checklist models.
     * @return mixed
     */
    public function actionGetAll()
    {

        $layout = 'main-panel';
        $template = 'get-all';
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $render = 'renderPartial';

        if (preg_match('/(android|iPhone|iPad|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
        {
            $layout = 'mobile-main-panel';
            $template = 'mobile-all';
            $render = 'render';
        }

        $this->layout = $layout;

        $lists = Todolist::find()->where(['user_id'=>Yii::$app->user->identity->id])->orderBy(['position'=>SORT_ASC])->all();
        $session = 0;
        if (Yii::$app->session->get('checklist'))
        {
            $session = Yii::$app->session->get('checklist');
        }
        return $this->$render($template, [
            'lists' => $lists,
            'session_checklist'=>$session
        ]);
    }

    public function actionClearDone($id)
    {
        if ($id==0)
        {
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id])->andWhere(['is','todolist_id', null])->andWhere(['done'=>1])->orderBy(['priority'=>SORT_ASC])->all();
        }else{
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id, 'todolist_id'=>$id])->andWhere(['done'=>1])->orderBy(['priority'=>SORT_ASC])->all();
        }
        foreach($all as $c)
        {
            $c->delete();
        }
         Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['success'=>'ok'];
        return $response;
    }

    public function actionAddTaskTodolist($task_id, $list_id)
    {
        if ($list_id)
            $list = Todolist::findOne($list_id);
        $task = Checklist::findOne($task_id);
        $old_list = $task->todolist_id;
        if ($list_id)
            $task->todolist_id = $list->id;
        else
            $task->todolist_id = null;
        $task->save();
        if ($old_list)
        {
            $q = Todolist::findOne($old_list)->getUndone();
        }else{
            $old_list = 0;
            $q = \common\models\Checklist::getNoListUndone();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($list_id)
            $response = ['task'=>$task_id, 'list'=>$list_id, 'quantity'=>$list->getUndone(), 'old'=>$old_list, 'oldquantity'=>$q];
        else
            $response = ['task'=>$task_id, 'list'=>$list_id, 'quantity'=>\common\models\Checklist::getNoListUndone()];
        return $response;    
    }

    public function actionSortDone($id, $sort_type=null)
    {
        if ($sort_type==1)
        {
            $stype=SORT_DESC;
            $sort_type=0;
        }else{
            $stype = SORT_ASC;
            $sort_type=1;
        }
        if ($id==0)
        {
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id])->andWhere(['is','todolist_id', null])->orderBy(['done'=>$stype, 'priority'=>SORT_ASC])->all();
            $name = Yii::t('app', 'Ogólne');
        }else{
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id, 'todolist_id'=>$id])->orderBy(['done'=>$stype, 'priority'=>SORT_ASC])->all();
            $name = Todolist::findOne($id)->name;
        }
        return $this->renderPartial('load-checklist', [
            'all' => $all,
            'id' => $id,
            'name'=>$name,
            'sort_type'=>$sort_type
        ]); 
    }

    public function actionDeleteTodolist($id)
    {
        $list = Todolist::findOne($id)->delete();
         Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id'=>$id];
        return $response;       
    }

    public function actionDeleteItem($id)
    {
        $list = Checklist::findOne($id)->delete();
         Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id'=>$id];
        return $response;       
    }

    public function actionLoadChecklist($id)
    {
        $layout = 'main-panel';
        $template = 'load-checklist';
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $render = 'renderPartial';

        if (preg_match('/(android|iPhone|iPad|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
        {
            $layout = 'mobile-main-panel';
            $template = 'load-checklist-mobile';
            $render = 'render';
        }

        $this->layout = $layout;

        Yii::$app->session->set('checklist', $id);
        if ($id==0)
        {
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id])->andWhere(['is','todolist_id', null])->orderBy(['priority'=>SORT_ASC])->all();
            $name = Yii::t('app', 'Ogólne');
        }else{
            $all = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id, 'todolist_id'=>$id])->orderBy(['priority'=>SORT_ASC])->all();
            $name = Todolist::findOne($id)->name;
        }  

        return $this->$render($template, [
            'all' => $all,
            'id' => $id,
            'name'=>$name,
            'sort_type'=>0
        ]); 
    }

    public function actionAddTodolist($name)
    {
        $list = new Todolist;
        $list->name = $name;
        $list->user_id = Yii::$app->user->identity->id;
        $list->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $list;
        return $response;
    }

    public function actionAddItem($id)
    {
        $list = new Checklist;
        if ($id!=0)
            $list->todolist_id = $id;
        else $id = null;
        $list->name = "";
        $list->user_id = Yii::$app->user->identity->id;
        $list->done = 0;
        $list->priority = Checklist::find()->where(['id'=>$id])->count()+1;
        $list->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $list;
        return $response;
    }

    public function actionEditItem($id)
    {
        $list = Checklist::findOne($id);
        $list->name = Yii::$app->request->post('name');
        $list->deadline = Yii::$app->request->post('time');
        $list->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $item['name'] = $list->name;
        $item['id'] = $list->id;
        $item['deadline'] = $list->deadline;
        $item['deadline_html'] = "";
        $item['done'] = $list->done;
        $today = date("Y-m-d H:i:s");
        if ($item['deadline']){
                        if ($item['deadline']<$today)
                        { 
                            $item['deadline_html'] = '<small class="label label-danger"><i class="fa fa-clock-o"></i>'.substr($item['deadline'], 0, 11).'</small>';
                         }else{
                        if (substr($item['deadline'], 0, 11)==substr($today, 0, 11)){ 
                            $item['deadline_html'] = '<small class="label label-primary"><i class="fa fa-clock-o"></i> '.Yii::t('app', 'dzisiaj').' ' .substr($item['deadline'], 11, 5).'</small>';
                         }else{ 
                            $item['deadline_html'] = '<small class="label label-info"><i class="fa fa-clock-o"></i> '.substr($item['deadline'], 0, 11).'</small>';
                         }
                        }
                }
        return $item;
    }


    public function actionIndex()
    {
        $searchModel = new ChecklistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Checklist model.
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
     
    public function actionDone($id)
    {
        $model = $this->findModel($id);
        if ($model->done) {
            $model->done = 0;
        }
        else {
            $model->done = 1;
        }
        $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
    }

    public function actionDeadline($id, $deadline)
    {
        $model = $this->findModel($id);
        ($deadline) ? $model->deadline = $deadline : $model->deadline = null;
        $model->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;
    }

    public function actionChangeTodo($id, $todo)
    {
        $model = $this->findModel($id);
        ($todo) ? $model->todolist_id = $todo : $model->todolist_id = null;
        $model->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;
    }

    public function actionOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('checklistitem') as $value) {
            $model = $this->findModel($value);
            $model->priority = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionOrderTodolist()
    {
        $i = 0;
        foreach (Yii::$app->request->post('todo') as $value) {
            $model = \common\models\Todolist::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }
    /**
     * Creates a new Checklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
        
    public function actionCreate()
    {
        $model = new Checklist();
        $model->user_id = Yii::$app->user->identity->id;
        $model->priority = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/site/index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Checklist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/site/index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Checklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }

     public function actionClear()
    {
        $models = Checklist::find()->where(['done'=>1])->all();
        foreach ($models as $model) {
             $model->delete();
        }
        return $this->redirect(['/site/index']);
    }   
    /**
     * Finds the Checklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Checklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Checklist::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
