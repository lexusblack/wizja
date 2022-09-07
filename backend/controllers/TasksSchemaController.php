<?php

namespace backend\controllers;

use Yii;
use common\models\TasksSchema;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TasksSchemaController implements the CRUD actions for TasksSchema model.
 */
class TasksSchemaController extends Controller
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
                        'actions' => ['index', 'project', 'create', 'update', 'delete'],
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
     * Lists all TasksSchema models.
     * @return mixed
     */
    public function actionIndex()
    {
        $projectSchemas = TasksSchema::find()->where(['type'=>1])->andWhere(['active'=>1])->all();
        $eventSchemas = TasksSchema::find()->where(['type'=>2])->andWhere(['active'=>1])->all();
        $rentalSchemas = TasksSchema::find()->where(['type'=>3])->andWhere(['active'=>1])->all();
        $meetingSchemas =TasksSchema::find()->where(['type'=>4])->andWhere(['active'=>1])->all();
        $serviceSchemas =TasksSchema::find()->where(['type'=>5])->andWhere(['active'=>1])->all();

        return $this->render('index', [
            'projectSchemas' => $projectSchemas,
            'eventSchemas' => $eventSchemas,
            'rentalSchemas' => $rentalSchemas,
            'meetingSchemas' => $meetingSchemas,
            'serviceSchemas' => $serviceSchemas,
        ]);
    }

    /**
     * Displays a single TasksSchema model.
     * @param integer $id
     * @return mixed
     */
    public function actionProject($id)
    {
        $model = $this->findModel($id);
        return $this->render('project', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new TasksSchema model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TasksSchema();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->default)
            {
                TasksSchema::updateAll(array( 'default' => 0 ), 'type = '.$model->type.' AND id<>'.$model->id );
            }
            return $this->redirect(['project', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TasksSchema model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->default)
            {
                TasksSchema::updateAll(array( 'default' => 0 ), 'type = '.$model->type.' AND id<>'.$model->id );
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TasksSchema model.
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
     * Finds the TasksSchema model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TasksSchema the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TasksSchema::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    

}
