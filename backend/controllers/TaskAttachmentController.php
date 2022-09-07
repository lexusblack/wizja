<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\TaskAttachment;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * TaskAttachmentController implements the CRUD actions for TaskAttachment model.
 */
class TaskAttachmentController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/task-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }



    /**
     * Creates a new TaskAttachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new TaskAttachment();
        $model->task_id = $id;

        if (Yii::$app->request->post()) {
            $formName = $model->formName();
            $post = Yii::$app->request->post($formName);
            $models = [];

            if ($post && is_array($post)) {
                foreach ($post as $formModels) {
                    if (is_array($formModels)) {
                        $newModel = new TaskAttachment();
                        foreach ($formModels as $prop => $val) {
                            $newModel->$prop = $val;
                        }
                        $models[] = $newModel;
                        $newModel->save();
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing TaskAttachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));

        return $this->redirect(['/task/view', 'id'=>$model->task_id, '#'=>'tab-attachment']);

    }

    /**
     * Finds the TaskAttachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaskAttachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new TaskAttachment();
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

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

    public function actionShow($id) {
        $model = $this->findModel($id);
        $filePath = $model->getFileUrl();
        $this->redirect(["../".$filePath]);
    }
}
