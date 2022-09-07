<?php

namespace backend\controllers;

use Yii;
use common\models\GearSimilar;
use common\models\GearSimilarSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GearSimilarController implements the CRUD actions for GearSimilar model.
 */
class GearSimilarController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all GearSimilar models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GearSimilarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearSimilar model.
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
     * Creates a new GearSimilar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gear_id)
    {
        $model = new GearSimilar();
        $model->gear_id = $gear_id;
        $model->both = 1;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->both==1)
            {
                $model2 = new GearSimilar;
                $model2->gear_id = $model->similar_id;
                $model2->similar_id = $model->gear_id;
                $model2->save();                
            }

            return $this->redirect(['/gear/view', 'id' => $model->gear_id, '#'=>'tab_similar']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearSimilar model.
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
     * Deletes an existing GearSimilar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $gear_id = $model->gear_id;
        $model->delete();

        return $this->redirect(['/gear/view', 'id'=>$gear_id, '#'=>'tab_similar']);
    }

    
    /**
     * Finds the GearSimilar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearSimilar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearSimilar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
