<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\Event;
use common\models\EventLog;
use Yii;
use common\models\Attachment;
use common\models\AttachmentSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttachmentController implements the CRUD actions for Attachment model.
 */
class AttachmentController extends Controller
{
    public $enableCsrfValidation = false;
    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['download', 'index'],
                    'roles' => ['eventEventEditEyeAttachmentDownload'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['eventEventEditEyeAttachmentAdd'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['update'],
                    'roles' => ['eventEventEditEyeAttachmentEdit'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['delete', 'delete-more'],
                    'roles' => ['eventEventEditEyeAttachmentDelete'],
                ],
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all Attachment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttachmentSearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Attachment model.
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
     * Creates a new Attachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($eventId=null)
    {
        $model = new Attachment();
        $model->event_id = $eventId;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if ($eventId !== null)
            {
                $eventlog = new EventLog;
                $eventlog->event_id =  $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Do eventu dodano załącznik").": ".$model->filename.".";
                $eventlog->save();
                return $this->redirect(Url::previous('event'));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('batchCreate', [
                'model' => $model,
            ]);
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new Attachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
                $eventlog = new EventLog;
                $eventlog->event_id =  $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Do eventu dodano załącznik").": ".$model->filename.".";
                $eventlog->save();           
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }

    /**
     * Updates an existing Attachment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['event/view', 'id' => $model->event_id, '#'=>'tab-attachment']);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Attachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));
                $eventlog = new EventLog;
                $eventlog->event_id = $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Z eventu usunięto załącznik").": ".$model->filename.".";
                $eventlog->save();
        return $this->redirect(['/event/view', 'id'=>$model->event_id, '#'=>'tab-attachment']);
    }

    public function actionDeleteMore()
    {
        $items = Yii::$app->request->post('items');
        foreach ($items as $id)
        {
            $model = $this->findModel($id);
            $model->delete();
            $eventlog = new EventLog;
            $eventlog->event_id = $model->event_id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Z eventu usunięto załącznik").": ".$model->filename.".";
            $eventlog->save();
        }
        exit;
    }

    /**
     * Finds the Attachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }


    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        if ($model->type == Attachment::TYPE_PANORAMA)
        {
            throw new ForbiddenHttpException(Yii::t('app', 'Nie można ściągnąć pliku'));
        }
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

    public function actionGallery($eventId)
    {
        $model = Event::findOne($eventId);
        if ($model===null)
        {
            throw new NotFoundHttpException();
        }
        $models = Attachment::find()
            ->where(['like', 'mime_type', 'image'])
            ->andWhere(['event_id'=>$eventId])
            ->all();


        return $this->render('gallery', [
            'model'=>$model,
            'models' => $models,
        ]);
    }

    public function actionShow($id)
    {
        $model = $this->findModel($id);
        $viewMap = [
            Attachment::TYPE_FILE => '_view',
            Attachment::TYPE_IMAGE => '_showGallery',
            Attachment::TYPE_PANORAMA => '_showPanorama'
        ];

        $view = $viewMap[$model->type];

        $showTools = $model->type==Attachment::TYPE_IMAGE ? false : true;
        return $this->render('show', ['model'=>$model, 'view'=>$view, 'showTools'=>$showTools]);
    }
}
