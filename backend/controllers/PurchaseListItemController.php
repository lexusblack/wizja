<?php

namespace backend\controllers;

use Yii;
use common\models\PurchaseListItem;
use PurchaseListItemSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PurchaseListItemController implements the CRUD actions for PurchaseListItem model.
 */
class PurchaseListItemController extends Controller
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
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'status', 'desc'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all PurchaseListItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchaseListItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatus($id)
    {
            $model = $this->findModel($id);
            $key = Yii::$app->request->post("editableIndex");
            $model->status = Yii::$app->request->post("PurchaseListItem")[$key]['status'];
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
                $output = ['output'=>\common\models\PurchaseListItem::getStatusList()[$model->status], 'message'=>''];
                return $output;
                exit;
    }

        public function actionDesc($id)
    {
            $model = $this->findModel($id);
            $key = Yii::$app->request->post("editableIndex");
            $att = Yii::$app->request->post("editableAttribute");
            if ($att=="description")
            {
                $model->description = Yii::$app->request->post("PurchaseListItem")[$key]['description'];
            }
            if ($att=="price")
            {
                $model->price = Yii::$app->request->post("PurchaseListItem")[$key]['price'];
            }
            if ($att=="quantity")
            {
                $model->quantity = Yii::$app->request->post("PurchaseListItem")[$key]['quantity'];
            }
            
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
                $output = ['output'=>Yii::$app->request->post("PurchaseListItem")[$key][$att], 'message'=>''];
                return $output;
                exit;
    }

    /**
     * Displays a single PurchaseListItem model.
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
     * Creates a new PurchaseListItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new PurchaseListItem();
        $model->purchase_list_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/purchase-list/view', 'id'=>$model->purchase_list_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PurchaseListItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/purchase-list/view', 'id'=>$model->purchase_list_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PurchaseListItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['/purchase-list/view', 'id'=>$model->purchase_list_id]);
    }

    
    /**
     * Finds the PurchaseListItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseListItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseListItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
