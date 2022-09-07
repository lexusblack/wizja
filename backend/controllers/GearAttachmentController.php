<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\GearAttachment;
use common\models\GearAttachmentSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * GearAttachmentController implements the CRUD actions for GearAttachment model.
 */
class GearAttachmentController extends Controller
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
                    'roles' => ['gearAttachments'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['gearAttachmentsCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'show', 'download', 'send', 'download-all'],
                    'roles' => ['gearAttachmentsView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['gearAttachmentsDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['gearAttachmentsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['upload'],
                    'roles' => ['gearCreate', 'gearEdit']
                ],
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/gear-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all GearAttachment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GearAttachmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSend($data)
    {
        $data = json_decode($data);
        $model = new \backend\models\SendFileMail();
        if ($model->load(Yii::$app->request->post()) && $model->validate()){

            $mail = \Yii::$app->mailer->compose('@app/modules/offers/views/default/mail', [
                'model' =>  $model,
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->user->identity->email])
            ->setTo($model->email2)
            ->setSubject($model->subject)
            ->setReplyTo(Yii::$app->user->identity->email);
            foreach ($data as $id)
            {
                $file = GearAttachment::findOne($id);
                $mail->attach($file->getFilePath()); 
            }
            if ($mail->send())
                return $this->redirect(['/gear/view', 'id'=>$file->gear_id, '#'=>'tab_attachments']);
            else
                echo "dd";
                exit;
        } 
        return $this->render('send-mail', [
            'model' => $model,
        ]);
    }

    public function actionDownloadAll($data)
    {
        $data = json_decode($data);
        $zip = new \ZipArchive();
        $t = time();
        $file = Yii::getAlias('@uploadroot/attachments'.$t.'.zip');
        if ($zip->open($file, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create a zip file');
        }

    foreach ($data as $id){
        $file = GearAttachment::findOne($id);
        $zip->addFile($file->getFilePath(), $file->filename);
    }

    $zip->close();
    return $this->redirect(Yii::getAlias('@uploads/attachments'.$t.'.zip'));
    }

    /**
     * Displays a single GearAttachment model.
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
     * Creates a new GearAttachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gearId=null)
    {
        $model = new GearAttachment();
        $model->gear_id = $gearId;

        if (Yii::$app->request->post()) {
            $formName = $model->formName();
            $post = Yii::$app->request->post($formName);
            $models = [];

            if ($post && is_array($post)) {
                foreach ($post as $formModels) {
                    if (is_array($formModels)) {
                        $newModel = new GearAttachment();
                        foreach ($formModels as $prop => $val) {
                            $newModel->$prop = $val;
                        }
                        $models[] = $newModel;
                        $newModel->save();
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['/gear/view', 'id' => $model->gear_id, '#'=>'tab_attachments']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GearAttachment model.
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
            return $this->redirect(['/gear/view', 'id' => $model->gear_id, '#'=>'tab_attachments']);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GearAttachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));

        return $this->redirect(['/gear/view', 'id'=>$model->gear_id, '#'=>'tab-attachments']);

    }

    /**
     * Finds the GearAttachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearAttachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearAttachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new GearAttachment();
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
