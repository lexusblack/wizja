<?php

namespace backend\controllers;

use Yii;
use common\models\Project;
use common\models\Note;

use common\models\ProjectUser;
use common\models\ProjectUserRole;
use common\models\ProjectSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-agency-offer', 'add-event', 'add-note', 'add-offer', 'add-project-department', 'add-project-user', 'add-task', 'add-task-category', 'add-file', 'add-to-project'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    public function actionAddToProject($id)
    {
        if (!Yii::$app->request->post())
        {
            $model = $this->findModel($id);
            $events = \common\helpers\ArrayHelper::map(\common\models\Event::find()->where(['project_id'=>null])->orderBy(['id'=>SORT_DESC])->asArray()->all(), 'id', 'name');
            return $this->renderAjax('add-to-project', [
            'model' => $model,
            'events' => $events,
        ]);
        }else{
            $ids = Yii::$app->request->post('Project')['event_ids'];
            foreach ($ids as $event_id)
            {
                $event = \common\models\Event::findOne($event_id);
                $event->project_id = $id;
                $event->save();
                
            }
            exit;
        }
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddFile($id)
    {
        $note_id = Note::createNote(1, 'projectAddFile', '', $id);
         return $this->redirect(['/note/add-file', 'id'=>$note_id]);
    }

    /**
     * Displays a single Project model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $projectUser = new ProjectUser();
        $projectUser->project_id = $id;
        if ($projectUser->load(Yii::$app->request->post()) && $projectUser->save()) {
            foreach (Yii::$app->request->post('ProjectUser')['roleIds'] as $role)
            {
                $pur = new ProjectUserRole();
                $pur->project_user_id = $projectUser->id;
                $pur->user_event_role_id = $role;
                $pur->save();
            }
            $projectUser = new ProjectUser();
            $projectUser->project_id = $id;
            
        }
        return $this->render('view', [
            'projectUser'=> $projectUser,
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->creator_id = Yii::$app->user->id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->linkObjects();
            if ($model->tasks_schema_id)
            {
                $model->copyTasks();
            }
            if (Yii::$app->request->post('Project')['managerIds'])
            {
                foreach (Yii::$app->request->post('Project')['managerIds'] as $user_id)
                {
                    $pu = new ProjectUser();
                    $pu->project_id = $model->id;
                    $pu->user_id = $user_id;
                    $pu->manager = 1;
                    $pu->save();
                }
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'schema_change_possible'=>true
            ]);
        }
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->linkObjects();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'schema_change_possible'=>false
            ]);
        }
    }

    /**
     * Deletes an existing Project model.
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
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    
    /**
    * Action to load a tabular form grid
    * for AgencyOffer
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddAgencyOffer()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('AgencyOffer');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formAgencyOffer', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for Event
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddEvent()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('Event');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formEvent', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for Note
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddNote()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('Note');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formNote', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for Offer
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddOffer()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('Offer');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formOffer', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for ProjectDepartment
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddProjectDepartment()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('ProjectDepartment');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formProjectDepartment', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for ProjectUser
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddProjectUser()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('ProjectUser');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formProjectUser', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for Task
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddTask()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('Task');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formTask', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for TaskCategory
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddTaskCategory()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('TaskCategory');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formTaskCategory', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
