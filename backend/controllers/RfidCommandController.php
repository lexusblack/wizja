<?php

namespace backend\controllers;

use Yii;
use common\models\RfidCommand;
use common\models\RfidAntenna;
use common\models\RfidCommandSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * RfidCommandController implements the CRUD actions for RfidCommand model.
 */
class RfidCommandController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
public function beforeAction($action) { $this->enableCsrfValidation = false; return parent::beforeAction($action); }

    /**
     * Lists all RfidCommand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RfidCommandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RfidCommand model.
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

    public function actionGetReaders()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new RfidCommand();
        $model->status = 0;
        $model->command = 'get-all-readers';
        $model->reader = 'FX950032FE46';
        $model->content = '';
        $model->save();
        return ['id'=>$model->id];
    }

    public function actionGetReadersAnswer($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $command = $this->findModel($id);
        $response = [];
        if ($command)
        {
            $readers = json_decode($command->info);
            if ($readers)
            {
            foreach ($readers as $r)
            {
                if ($r!=""){
                    $antena = RfidAntenna::find()->where(['code'=>$r->Reader])->one();
                    if ($antena)
                    {
                        $name = $antena->name;
                    }else{
                        $name = Yii::t('app', 'Bez nazwy');
                    }
                    if ($r->Status=='registered')
                    {
                        $status = 1;
                    }else{
                        $status = 2;
                    }
                    $response[] = ['id'=>$r->Reader, 'name'=>$name, 'status'=>$status];
                }
            }
            }
            return ['id'=>$command->reader,'readers'=>$response, 'status'=>$command->status];
        }else{
            return ['id'=>"",'readers'=>$response, 'status'=>0];
        }

    }

    public function actionStartReading($reader)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new RfidCommand();
        $model->status = 0;
        $model->command = 'start-reading';
        $model->reader = $reader;
        $model->content = '';
        $model->save();
        return ['id'=>$model->id];
    }

    public function actionRegisterReader($reader)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new RfidCommand();
        $model->status = 0;
        $model->command = 'register-reader';
        $model->reader = $reader;
        $model->content = '';
        $model->save();
        return ['id'=>$model->id];
    }

    public function actionStopReading($id)
    {
        $model = new RfidCommand();
        $model->status = 0;
        $model->command = 'stop-reading';
        $model->reader = $id;
        $model->content = '';
        $model->save();
        exit;
    }

    /**
     * Creates a new RfidCommand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RfidCommand();
        $model->status = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RfidCommand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RfidCommand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGet()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        date_default_timezone_set(Yii::$app->params['timeZone']);

        $now =  date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'))-1000);
        $commands = RfidCommand::find()->where(['status'=>0])->andWhere(['>', 'create_time', $now])->all();
        $commandsArray = [];
        foreach ($commands as $c)
        {
            $cmd = [];
            $cmd['Id'] = $c->id;
            $cmd['Reader'] = $c->reader;
            $cmd['Command'] = $c->command;
            $cmd['Content'] = $c->content;
            $commandsArray[] = $cmd;
        }
        return ['Commands'=>$commandsArray];
    }

    public function actionResponse()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('Id');
        $content =  Yii::$app->request->post('Content');
        $command = $this->findModel($id);
        if ($command)
        {
            if ($command->command =='get-all-readers')
            {
                $readers = $content['Readers'];
                $info = "";
                foreach ($readers as $r)
                {
                    $info .= $r['Reader'].";";
                }
                $command->info = json_encode($readers);
                if ($info!="")
                    {
                        $command->status = 1;
                    }else{
                        $command->status = 2;
                    }
                $command->save();
            }else{
                if ($content['Status']=="ok")
                    {
                        $command->status = 1;
                    }else{
                        $command->status = 2;
                    }
                    $command->info = $content['Info'];
                    $command->save();
                    }

        }
        return ['Status'=>'ok', 'Info'=>null];   
    }

    
    /**
     * Finds the RfidCommand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RfidCommand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RfidCommand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
