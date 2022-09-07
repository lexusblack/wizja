<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\Event;
use Yii;
use common\models\Estimate;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EstimateController implements the CRUD actions for Estimate model.
 */
class EstimateController extends Controller
{
    public $enableCsrfValidation = false;
    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/estimate',
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
                    'actions' => ['download'],
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
                    'actions' => ['delete'],
                    'roles' => ['eventEventEditEyeAttachmentDelete'],
                ],
            ]
        ];

        return $behaviors;
    }

    /**
     * Creates a new Estimate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($eventId=null)
    {
        $model = new Estimate();
        $model->event_id = $eventId;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));

            return $this->redirect(['/event/view', 'id' => $model->event_id, '#'=>'tab-estimate']);
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
        $model = new Estimate();
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

    /**
     * Updates an existing Estimate model.
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
     * Deletes an existing Estimate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));
        return $this->redirect(['/event/view', 'id'=>$model->event_id, '#'=>'tab-estimate']);
    }

    /**
     * Finds the Estimate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Estimate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Estimate::findOne($id)) !== null) {
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
}
