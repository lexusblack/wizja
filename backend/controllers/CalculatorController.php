<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Calculator;
use common\models\CalculatorSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CalculatorController implements the CRUD actions for Calculator model.
 */
class CalculatorController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['menuToolboxBlend'],
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    /**
     * Lists all Calculator models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CalculatorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $settings = \Yii::$app->settings;
        $value = $settings->get('main.companyLogo');
        $url = null;
        if($value){
            $url ="./..".\Yii::getAlias('@uploads' . '/settings/').$value;
        }else{
            $url = './../img/logo.png';
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'companyLogo' =>$url
        ]);
    }

    /**
     * Displays a single Calculator model.
     * @param integer $id
     * @return mixed
     */
    public function actionGetconfig($id)
    {
            $model = $this->findModel($id);
            echo $model->config;
            Yii::$app->end();   
    }

    public function actionAjax()
        {
            $data = Yii::$app->request->post('test');
            if (isset($data)) {
                $test = "Ajax Worked!";
            } else {
                $test = "Ajax failed";
            }
            return \yii\helpers\Json::encode($test);
        }
    
    public function actionSaveconfig()
        {
            $dataDecoded = Yii::$app->request->post('data');
            //$dataDecoded = \yii\helpers\Json::decode($data); 
            if ($dataDecoded['id']!="")
            {
                $model = $this->findModel($dataDecoded['id']);
                $model->config = \yii\helpers\Json::encode($dataDecoded);
                $model->name = $dataDecoded['name'];
                $model->save();
                return \yii\helpers\Json::encode(["message"=>"Zapisano!"]);
            }else{
                $model = new Calculator();
                $model->config = \yii\helpers\Json::encode($dataDecoded);
                $model->name = $dataDecoded['name'];
                $model->user_id = Yii::$app->user->getId();
                $model->save();
                return \yii\helpers\Json::encode(["message"=>"Zapisano!"]);
            }

        }
    /**
     * Creates a new Calculator model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Calculator();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
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
     * Updates an existing Calculator model.
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
     * Deletes an existing Calculator model.
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
     * Finds the Calculator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Calculator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Calculator::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
