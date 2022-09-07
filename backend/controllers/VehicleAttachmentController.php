<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\VehicleAttachment;
use common\models\VehicleAttachmentSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * VehicleAttachmentController implements the CRUD actions for VehicleAttachment model.
 */
class VehicleAttachmentController extends Controller
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
                    'roles' => ['fleetAttachments'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['fleetAttachmentsCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['fleetAttachmentsView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['fleetAttachmentsDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['fleetAttachmentsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['download'],
                    'roles' => ['fleetAttachmentsDownload']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [
            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/vehicle-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],
            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all VehicleAttachment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehicleAttachmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VehicleAttachment model.
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
     * Creates a new VehicleAttachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=null)
    {
        $model = new VehicleAttachment();
        $model->vehicle_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {

            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if (Url::previous())
            {
                return $this->redirect(Url::previous());
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing VehicleAttachment model.
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
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing VehicleAttachment model.
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
     * Finds the VehicleAttachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleAttachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VehicleAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new VehicleAttachment();
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
