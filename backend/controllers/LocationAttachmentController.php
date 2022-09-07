<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\Attachment;
use Yii;
use common\models\LocationAttachment;
use common\models\LocationAttachmentSearch;
use backend\components\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * LocationAttachmentController implements the CRUD actions for LocationAttachment model.
 */
class LocationAttachmentController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index'],
                    'roles' => ['locationLocationsViewAttachments'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['locationAttachmentsAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['locationAttachmentsView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['locationAttachmentsDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'upload'],
                    'roles' => ['locationAttachmentsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['show'],
                    'roles' => ['locationAttachmentsView']
                ],
                [
                    'allow' => true,
                    'actions' => ['download'],
                    'roles' => ['locationLocationsViewAttachmentsDownload']
                ]
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/location',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all LocationAttachment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationAttachmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LocationAttachment model.
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
     * Creates a new LocationAttachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($locationId=null)
    {
        $model = new LocationAttachment();
        $model->location_id = $locationId;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if ($locationId !== null)
            {
                return $this->redirect(Url::previous('location'));
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('batchCreate', [
                'model' => $model,
            ]);
//            return $this->render('create', [
//                'model' => $model,
//            ]);
        }
    }

    /**
     * Updates an existing LocationAttachment model.
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
            return $this->redirect(['location/view', 'id' => $model->location_id ]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LocationAttachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));
        return $this->redirect(Url::previous('location'));


    }

    /**
     * Finds the LocationAttachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationAttachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocationAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        if ($model->type == LocationAttachment::TYPE_PANORAMA)
        {
            throw new ForbiddenHttpException(Yii::t('app', 'Nie można ściągnąć tego rodzaju pliku'));
        }
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

    public function actionShow($id)
    {
        $model = $this->findModel($id);
        $viewMap = [
            LocationAttachment::TYPE_FILE => '_view',
            LocationAttachment::TYPE_IMAGE => '_showGallery',
            LocationAttachment::TYPE_PANORAMA => '_showPanorama'
        ];

        $view = $viewMap[$model->type];

        $showTools = $model->type==LocationAttachment::TYPE_IMAGE ? false : true;
        return $this->render('show', ['model'=>$model, 'view'=>$view, 'showTools'=>$showTools]);
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new LocationAttachment();
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
