<?php

namespace backend\controllers;

use Yii;
use common\models\VehicleModel;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VehicleModelController implements the CRUD actions for VehicleModel model.
 */
class VehicleModelController extends Controller
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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index'],
                    'roles' => ['fleetVehicles'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'update', 'add-vehicle-price'],
                    'roles' => ['fleetVehiclesCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['fleetVehiclesView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['fleetVehiclesDelete'],
                ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all VehicleModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => VehicleModel::find()->where(['active'=>1]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VehicleModel model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('VehiclePrice')){
            if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
                    return $this->redirect(['index']);
        } 
        }
        $providerVehiclePrices = new \yii\data\ArrayDataProvider([
            'allModels' => $model->vehiclePrices,
        ]);
        return $this->render('view', [
            'model' => $model,
            'providerVehiclePrice' => $providerVehiclePrices,
        ]);
    }

    /**
     * Creates a new VehicleModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VehicleModel();
        $model->active = 1;
        $model->position = VehicleModel::find()->where(['active'=>1])->count()+1;
        if (Yii::$app->request->post('VehiclePrice')){
            if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing VehicleModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('VehiclePrice')){
            if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
             return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing VehicleModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();
        return $this->redirect(['index']);
    }

    
    /**
     * Finds the VehicleModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VehicleModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

        public function actionAddVehiclePrice()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('VehiclePrice');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formVehiclePrice', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
