<?php

namespace backend\controllers;

use Yii;
use common\models\TaskSchema;
use common\models\TaskSchemaNotification;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TaskSchemaController implements the CRUD actions for TaskSchema model.
 */
class TaskSchemaController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'order', 'add-notification', 'delete-notification', 'edit-notification'],
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
     * Lists all TaskSchema models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TaskSchema::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaskSchema model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionOrder()
    {
        $i = 1;
        foreach (Yii::$app->request->post('item') as $value) {
            $model = $this->findModel($value);
            $model->order = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionAddNotification($id)
    {
        $taskSchema = $this->findModel($id);
        $model = new TaskSchemaNotification();
        $model->task_schema_id = $id;
        $model->time = 1;
        $model->time_type = 3;
        $model->text = Yii::t('app', 'Masz zadanie do wykonania.')."<br/>".$taskSchema->name;
        $model->save();
        exit;
    }

    public function actionEditNotification($id)
    {
        $model = TaskSchemaNotification::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }
        exit;
    }

    public function actionDeleteNotification($id)
    {
        $model = TaskSchemaNotification::findOne($id);
        $model->delete();
        exit;


    }

    /**
     * Creates a new TaskSchema model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($category_id)
    {
        $model = new TaskSchema();
        $model->tasks_schema_cat_id = $category_id;
        $model->time_type=1;
        $model->hours=0;
        $model->minutes = 0;
        $model->days = 0;
        $model->order = TaskSchema::find()->where(['tasks_schema_cat_id'=>$category_id])->count()+1;

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->linkObjects();
            $model = TaskSchema::find()->where(['id'=>$model->id])->asArray()->one();
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
     * Updates an existing TaskSchema model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->linkObjects();
            $model = TaskSchema::find()->where(['id'=>$model->id])->asArray()->one();
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
     * Deletes an existing TaskSchema model.
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
     * Finds the TaskSchema model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaskSchema the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskSchema::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
