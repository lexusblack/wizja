<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\CustomerAttachment;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * CustomerAttachmentController implements the CRUD actions for CustomerAttachment model.
 */
class CustomerAttachmentController extends Controller
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
                'upload'=>'/customer-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all CustomerAttachment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerAttachmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerAttachment model.
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
     * Creates a new CustomerAttachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customerId)
    {
        $model = new CustomerAttachment();
        $model->customer_id = $customerId;

        if (Yii::$app->request->post()) {
            $formName = $model->formName();
            $post = Yii::$app->request->post($formName);
            $models = [];

            if ($post && is_array($post)) {
                foreach ($post as $formModels) {
                    if (is_array($formModels)) {
                        $newModel = new CustomerAttachment();
                        foreach ($formModels as $prop => $val) {
                            $newModel->$prop = $val;
                        }
                        $models[] = $newModel;
                        $newModel->save();
                    }
                }
            }
                return $this->redirect(['customer/view', 'id' => $customerId]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomerAttachment model.
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
            if (Url::previous() != null)
            {
                $this->redirect(Url::previous());
            }
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
     * Deletes an existing CustomerAttachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));

        return $this->redirect(['/customer/view', 'id'=>$model->customer_id, '#'=>'tab-attachment']);

    }

    /**
     * Finds the CustomerAttachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerAttachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new CustomerAttachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        echo var_dump($data['params']);
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
