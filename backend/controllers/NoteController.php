<?php

namespace backend\controllers;

use Yii;
use common\models\Note;
use common\models\NoteAttachment;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class NoteController extends Controller
{
    
    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/note-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-note', 'add-note-attachment', 'send', 'add-file', 'delete-file', 'upload', 'add-comment', 'save-comment', 'get-comments'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    public function actionSend($project_id=null, $type, $event_id=null, $gear_id=null)
    {
        $model = new Note();
        $model->type = $type;
        $model->project_id = $project_id;
        $model->event_id = $event_id;
        $model->gear_id = $gear_id;
        $model->text = Yii::$app->request->post('text');
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $model->datetime = date('Y-m-d H:i:s');
        $model->user_id = Yii::$app->user->id;
        $model->save();
        exit;
    }

    public function actionSaveComment($id)
    {
        $oldModel = $this->findModel($id);
        $model = new Note();
        $model->attributes = $oldModel->attributes;
        $model->text = Yii::$app->request->post('text');
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $model->datetime = date('Y-m-d H:i:s');
        $model->user_id = Yii::$app->user->id;
        $model->note_id = $id;
        $model->auto = 0;
        $model->save();
        exit;
    }

    public function actionGetComments($id)
    {
        $oldModel = $this->findModel($id);
        return $this->renderAjax('get-comments', ['m' => $oldModel]);
    }

    /**
     * Lists all Note models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null)
    {
        $searchModel = new \common\models\NoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['noteparams'];
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
        Yii::$app->session['noteparams'] = $params;
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

    /**
     * Displays a single Note model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerNote = new \yii\data\ArrayDataProvider([
            'allModels' => $model->notes,
        ]);
        $providerNoteAttachment = new \yii\data\ArrayDataProvider([
            'allModels' => $model->noteAttachments,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerNote' => $providerNote,
            'providerNoteAttachment' => $providerNoteAttachment,
        ]);
    }

    /**
     * Creates a new Note model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Note();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Note model.
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
     * Deletes an existing Note model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $oldModel = $model;
        $model->delete();
        if ( $oldModel->type==1)
        {
            return $this->redirect(['/project/view', 'id'=>$oldModel->project_id, '#'=>'tab-note']);
        }

        
    }

    
    /**
     * Finds the Note model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Note the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Note::findOne($id)) !== null) {
            return $model;
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
            return $this->renderAjax('_formNoteComment', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionAddComment($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formNoteComment', ['id' => $id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for NoteAttachment
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddNoteAttachment()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('NoteAttachment');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formNoteAttachment', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDeleteFile($id)
    {
        $model = NoteAttachment::findOne($id);
        $oldModel = $model->note;
        $model->delete();
        if ( $oldModel->type==1)
        {
            return $this->redirect(['/project/view', 'id'=>$oldModel->project_id, '#'=>'tab-note']);
        }
            return $this->redirect(['/site/index']);
    }

    /**
     * Deletes an existing CustomerNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAddFile($id)
    {
        $model = new NoteAttachment();
        $model->note_id = $id;
        $oldModel = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
        if ( $oldModel->type==1)
        {
            return $this->redirect(['/project/view', 'id'=>$oldModel->project_id, '#'=>'tab-note']);
        }
        if ( $oldModel->type==2)
        {
            return $this->redirect(['/event/view', 'id'=>$oldModel->event_id, '#'=>'tab-notes']);
        }
            return $this->redirect(['/site/index']);
        }
        else
        {
            return $this->render('batchCreate', [
                'model' => $model,
                'note'=> $oldModel
            ]);
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new NoteAttachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {           
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }
}
