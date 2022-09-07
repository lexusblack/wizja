<?php

namespace backend\controllers;

use Yii;
use common\models\Service;
use common\models\OfferSchema;
use common\models\ServiceCategory;
use common\models\ServiceSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends Controller
{
    public function actionProject($id)
    {
        $model = OfferSchema::findOne($id);
        $categories = ServiceCategory::find()->where(['schema_id'=>$id])->orderBy(['position'=>SORT_ASC])->all();
        return $this->render('project', [
            'categories' => $categories,
            'model' => $model
        ]);        
    }
    /**
     * Lists all Service models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Service model.
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

    public function actionOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('item') as $value) {
            $model = $this->findModel($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionVisible($id)
    {
        $model = $this->findModel($id);
        if ($model->in_offer==1)
            $model->in_offer = 0;
        else
            $model->in_offer = 1;
        Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            $model->save();
            return $response;
    }

    /**
     * Creates a new Service model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($category_id=null)
    {
        $model = new Service();
        $model->service_category_id = $category_id;
        $model->position = Service::find()->where(['service_category_id'=>$category_id])->count()+1;
        $model->in_offer = 1;
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = Service::find()->where(['id'=>$model->id])->asArray()->one();
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
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Service model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteService($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id'=>$id];
        return $response;
    }

    
    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
