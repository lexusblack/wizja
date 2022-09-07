<?php

namespace backend\controllers;

use Yii;
use common\models\Schedule;
use common\models\ScheduleType;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ScheduleController implements the CRUD actions for Schedule model.
 */
class ScheduleController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'order', 'all', 'load-to-offer', 'delete-type', 'create-type', 'update-type'],
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
     * Lists all Schedule models.
     * @return mixed
     */

    public function actionLoadToOffer($type)
    {
        if ($type!=1000000)
        {
            $models = Schedule::find()->where(['schedule_type_id'=>$type])->orderBy(['position'=>SORT_ASC])->all();
        }else{
            $models = null;
        }

        return $this->renderAjax('load-to-offer', ['models'=>$models]);
    }

    public function actionAll()
    {
        $eventTypes = \common\models\ScheduleType::find()->all();
        return $this->render('all', [
            'eventTypes'=>$eventTypes
        ]);
    }
    public function actionIndex($id)
    {
        $eventType = \common\models\ScheduleType::findOne($id);
        $models = Schedule::find()->where(['schedule_type_id'=>$id])->all();

        return $this->render('index', [
            'models'=>$models,
            'eventType'=>$eventType
        ]);
    }

    /**
     * Displays a single Schedule model.
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
     * Creates a new Schedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Schedule();
        $model->schedule_type_id = $type;
        $model->is_required = 0;
        $model->book_gears = 1;
        $model->position = Schedule::find()->where(['schedule_type_id'=>$type])->count();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $type]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

        public function actionCreateType()
    {
        $model = new ScheduleType();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create-type', [
                'model' => $model,
            ]);
        }
    }

        public function actionUpdateType($id)
    {
        $model = ScheduleType::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update-type', [
                'model' => $model,
            ]);
        }
    }

        public function actionDeleteType($id)
    {
        $model = ScheduleType::findOne($id);
        $model->delete();
        Schedule::deleteAll(['schedule_type_id'=>$id]);
        return $this->redirect(['all']);
    }

    /**
     * Updates an existing Schedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->schedule_type_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Schedule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index', 'id' => $model->schedule_type_id]);
    }

    public function actionOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = $this->findModel($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    
    /**
     * Finds the Schedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Schedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Schedule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
