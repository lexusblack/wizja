<?php

namespace backend\controllers;

use Yii;
use common\models\TasksSchemaCat;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TasksSchemaCatController implements the CRUD actions for TasksSchemaCat model.
 */
class TasksSchemaCatController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-task-schema', 'order'],
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
     * Lists all TasksSchemaCat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TasksSchemaCat::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TasksSchemaCat model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerTaskSchema = new \yii\data\ArrayDataProvider([
            'allModels' => $model->taskSchemas,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerTaskSchema' => $providerTaskSchema,
        ]);
    }


    public function actionCreate($schema_id)
    {
        $model = new TasksSchemaCat();
        $model->tasks_schema_id = $schema_id;
        $model->order = TasksSchemaCat::find()->where(['tasks_schema_id'=>$schema_id])->count()+1;
        $model->color = "#273a4a";

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = TasksSchemaCat::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                        'model' => $model
            ]);
        }
    }

    public function actionOrder()
    {
        $i = 1;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = $this->findModel($value);
            $model->order = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    /**
     * Updates an existing TasksSchemaCat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = TasksSchemaCat::find()->where(['id'=>$model->id])->asArray()->one();
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
     * Deletes an existing TasksSchemaCat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id'=>$id];
        return $response;
    }

    
    /**
     * Finds the TasksSchemaCat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TasksSchemaCat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TasksSchemaCat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for TaskSchema
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddTaskSchema()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('TaskSchema');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formTaskSchema', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
