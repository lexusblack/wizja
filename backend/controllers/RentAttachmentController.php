<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\Event;
use common\models\RentLog;
use Yii;
use common\models\RentAttachment;
use common\models\RentAttachmentSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttachmentController implements the CRUD actions for Attachment model.
 */
class RentAttachmentController extends Controller
{
    public $enableCsrfValidation = false;
    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/rent',
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
                    'actions' => ['download', 'show'],
                    'roles' => ['eventRentsEdit'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['eventRentsEdit'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['update'],
                    'roles' => ['eventRentsEdit'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['delete'],
                    'roles' => ['eventRentsEdit'],
                ],
            ]
        ];

        return $behaviors;
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
    public function actionCreate($rentId=null)
    {
        $model = new RentAttachment();
        $model->rent_id = $rentId;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if ($rentId !== null)
            {
                $eventlog = new RentLog;
                $eventlog->rent_id =  $model->rent_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Do wypożyczenia dodano załącznik").": ".$model->filename.".";
                $eventlog->save();
                return $this->redirect(Url::previous('event'));
            }
            return $this->redirect(['/rent/view', 'id'=>$model->rent_id, '#'=>'tab-attachment']);
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
        $model = new RentAttachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
                $eventlog = new RentLog;
                $eventlog->rent_id =  $model->rent_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Do wypożyczenia dodano załącznik").": ".$model->filename.".";
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
            return $this->redirect(['rent/view', 'id' => $model->rent_id, '#'=>'tab-attachment']);
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
                $eventlog = new RentLog;
                $eventlog->rent_id =  $model->rent_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Z wypożyczenia usunięto załącznik").": ".$model->filename.".";
                $eventlog->save();
        return $this->redirect(['/rent/view', 'id'=>$model->rent_id, '#'=>'tab-attachment']);
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
        if (($model = RentAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionShow($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(Yii::getAlias('@uploads/rent/'.$model->filename));
    }


    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

}
