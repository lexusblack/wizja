<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\components\Rsa;
use common\components\Math_BigInteger;
use common\models\Timer;
use common\models\TimerSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\LoginForm;

/**
 * TimerController implements the CRUD actions for Timer model.
 */
class TimerController extends Controller
{
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/timer'

            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['menuToolboxShowTime'],
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Timer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TimerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Timer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Timer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $login = new LoginForm();
        $login->username = Yii::$app->request->post('username');
        $login->password = Yii::$app->request->post('password');
        if ($login->login())
        {
            $model = new Timer();
            $filename = mktime().".e4e";
            $content = Yii::$app->request->post('content');
            file_put_contents(Yii::getAlias('@uploadroot/timer/').$filename, $content);
            $model->filename = $filename;
            $model->name = Yii::$app->request->post('name');
            if ($model->saveAll()) {
                 $return['status'] = true;
            } else {
                $return['status'] = false;
            }           
        }else{
            $return['status'] = false;
        }

        $return = \yii\helpers\Json::encode($return);
        return $return;
    }

    /**
     * Updates an existing Timer model.
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
     * Deletes an existing Timer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }

    public function actionTimer()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $return['status'] = true;
            $return['datetime'] = date('Y-m-d H:i:s');
            $return['version'] = '1.0.0.0';
            $return['user_id'] = Yii::$app->user->identity->id;
            $data = $return['datetime']."|".$return['version']."|".$return['user_id'];
            //$private_key = file_get_contents(Yii::$app->basePath.'/web/private.key', FILE_USE_INCLUDE_PATH);
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCw2KypSQTfzzqok62BMhKshht3cL44fSiFT8fn2QgSC7ICbCN7
33jfQiciShs6DN26llER9XuG3oAx7aUyt1iTWlpuaI25CQstPfguzRwhvXk0t6AE
6G/McOHtEjacrsfKTASdvFrXFPFQS+ppaAf2w7eyBJSuayKQYHoix0mP+wIDAQAB
AoGAPN+ot3DiE6RCncqPu9wfn3FePQP7BnjWnOT0e/MyGvwZn0nYAQjQk5Ey5VO7
AYVyQYsChvsINUmbuRQDfGyuOSAXmeBu6URwSHWGev7+U++BgdTRuNy2Ez49REDn
QTJtVP2zD7RTL1U8JV7ySmw2SAT7/9djXRsF6Q4YqXaLKJkCQQDc8DK8/Kdiz6vv
BEzPRoSJ8xsayZF9kPUZhtC6rWMzR7pvqsERk0jiH0klVO82NZsW+u0aVt/bx0rw
qyy0vVK3AkEAzOk02YGAuyy1XSwj7fRyi8LRyY3Q1z6qFO4eZN07mMWqfQ+kLwIG
O62DtJ6buOly3fnqEGQih+xjiwBjCKgY3QJBAJkGhR4AoK7/x8Y05D5sSUCC8TMM
iYi+7gRQLCIgFaVe+PJ/Alp5+PElWjRRL54MYu73vWGQ6lv/HRi0drJ4ruECQBVy
Ftjox9tPG5ArzXrbCZ38/s3UbNYKNezI2x99U/5yOZyrJWjSEmruhwlBTFT3AdGf
lVKv2DlXkTd8C+FdDnUCQGSMOjzEFGkAd7JVD2Ix176d5Uvh/nkC6jb/IkKn1nnG
cova/T0K65V21PgSig3Xty6be3adQH+pkC4FSWjRpQ0=
-----END RSA PRIVATE KEY-----
EOD;
$public_key = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCw2KypSQTfzzqok62BMhKshht3
cL44fSiFT8fn2QgSC7ICbCN733jfQiciShs6DN26llER9XuG3oAx7aUyt1iTWlpu
aI25CQstPfguzRwhvXk0t6AE6G/McOHtEjacrsfKTASdvFrXFPFQS+ppaAf2w7ey
BJSuayKQYHoix0mP+wIDAQAB
-----END PUBLIC KEY-----
EOD;
            openssl_sign($data, $return['sign'], $private_key, OPENSSL_ALGO_SHA1);
            //$r = openssl_verify($data, $return['sign'], $public_key, OPENSSL_ALGO_SHA1);
            //$return['verify'] = $r;
            $return['sign'] =base64_encode($return['sign']);
            //$return['sign'] = sha1($return['datetime']."|".$return['version']."|".$return['user_id']."|"."NJ&<TG]k3%(C]Zy");
            $return = \yii\helpers\Json::encode($return);

        } else {
            $return['status'] = false;
            $return = \yii\helpers\Json::encode($return);
        }
        /*
        
        $rsa = new Rsa();
        $rsa->loadKey($private_key);
        $ciphertext = $rsa->encrypt($return);
        */
        return $return;
    }

    public function actionDownload($id)
    {
        
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        $filePath = $model->getFileUrl();
        //$this->redirect(["../".$filePath]);
        return Yii::$app->response->sendFile($filePath);
        //Yii::$app->end();
    }  
    /**
     * Finds the Timer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Timer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Timer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
