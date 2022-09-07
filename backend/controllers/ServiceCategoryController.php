<?php

namespace backend\controllers;

use Yii;
use common\models\ServiceCategory;
use common\models\ServiceCategorySearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
/**
 * ServiceCategoryController implements the CRUD actions for ServiceCategory model.
 */
class ServiceCategoryController extends Controller
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

    /**
     * Lists all ServiceCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrder()
    {
        $i = 1;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = $this->findModel($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    /**
     * Displays a single ServiceCategory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerService = new \yii\data\ArrayDataProvider([
            'allModels' => $model->services,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerService' => $providerService,
        ]);
    }

    /**
     * Creates a new ServiceCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($schema_id)
    {
        $model = new ServiceCategory();
        $model->schema_id = $schema_id;
        $model->position = ServiceCategory::find()->where(['schema_id'=>$schema_id])->count()+1;
        $model->in_offer = 1;
        $model->color = "#273a4a";

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = ServiceCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing ServiceCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->color)
            $model->color = "#273a4a";
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model = ServiceCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing ServiceCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteCategory($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id'=>$id];
        return $response;
    }

    
    /**
     * Finds the ServiceCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServiceCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for Service
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddService()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('Service');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formService', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
