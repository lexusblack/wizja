<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\HallGroupPhoto;
use common\models\HallGroupPhotoSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * HallGroupPhotoController implements the CRUD actions for HallGroupPhoto model.
 */
class HallGroupPhotoController extends Controller
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
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['@'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'show', 'download', 'send', 'download-all'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['upload'],
                    'roles' => ['@']
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
                'upload'=>'/halls',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all HallGroupPhoto models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HallGroupPhotoSearch();
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
                $file = HallGroupPhoto::findOne($id);
                $mail->attach($file->getFilePath()); 
            }
            if ($mail->send())
                return $this->redirect(['/hall-group/view', 'id'=>$file->hall_group_id, '#'=>'tab_attachments']);
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
        $file = HallGroupPhoto::findOne($id);
        $zip->addFile($file->getFilePath(), $file->filename);
    }

    $zip->close();
    return $this->redirect(Yii::getAlias('@uploads/attachments'.$t.'.zip'));
    }

    /**
     * Displays a single HallGroupPhoto model.
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
     * Creates a new HallGroupPhoto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($hall_group_id=null)
    {
        $model = new HallGroupPhoto();
        $model->hall_group_id = $hall_group_id;

        if (Yii::$app->request->post()) {

            $formName = $model->formName();
            $post = Yii::$app->request->post($formName);
            $models = [];

            if ($post && is_array($post)) {
                foreach ($post as $formModels) {
                    if (is_array($formModels)) {
                        $newModel = new HallGroupPhoto();
                        foreach ($formModels as $prop => $val) {
                            $newModel->$prop = $val;
                        }
                        $models[] = $newModel;
                        $newModel->hall_group_id = intval($hall_group_id);
                        echo var_dump($newModel);
                        
                        $newModel->save();
                        exit;
                    }
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                 return $this->redirect(['/hall-group/view', 'id'=>$model->hall_group_id, '#'=>'tab_attachments']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HallGroupPhoto model.
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
             return $this->redirect(['/hall-group/view', 'id'=>$model->hall_group_id, '#'=>'tab_attachments']);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing HallGroupPhoto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));

         return $this->redirect(['/hall-group/view', 'id'=>$model->hall_group_id, '#'=>'tab_attachments']);

    }

    /**
     * Finds the HallGroupPhoto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HallGroupPhoto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HallGroupPhoto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new HallGroupPhoto();
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
