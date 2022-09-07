<?php

namespace backend\controllers;

use Yii;
use common\models\EventStatut;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventStatutController implements the CRUD actions for EventStatut model.
 */
class EventStatutController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'order'],
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
     * Lists all EventStatut models.
     * @return mixed
     */
    public function actionIndex($type=1)
    {
        $models = EventStatut::find()->where(['active'=>1, 'type'=>$type])->orderBy(['position'=>SORT_ASC])->all();
        return $this->render('index', [
            'models' => $models,
            'type' => $type
        ]);
    }

    /**
     * Displays a single EventStatut model.
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
     * Creates a new EventStatut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type=1)
    {
        $model = new EventStatut();
        $model->type = $type;
        $model->active = 1;
        $model->position = EventStatut::find()->where(['type'=>$type])->count();
        $model->blocks_costs = 0;
        $model->blocks_working_times = 0;
        $model->blocks_status_revert = 0;
        $model->blocks_gear = 0;
        $model->blocks_event = 0;
        $model->count_provision = 0;
        $model->button = 0;
        $model->reminder = 0;
        $model->reminder_sms = 0;
        $model->reminder_mail = 0;
        $model->delete_gear = 0;
        $model->delete_crew = 0;
        $model->delete_task = 0;
        $model->color = '#273a4a';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'type'=>$type]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventStatut model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->users = explode(";",$model->reminder_users);
        $model->roles = explode(";",$model->reminder_roles);
        $model->permissions = explode(";",$model->permission_users);
        if (!$model->delete_gear)
            $model->delete_gear = 0;
        if (!$model->delete_crew)
            $model->delete_crew = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'type'=>$model->type]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
     * Deletes an existing EventStatut model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();

        return $this->redirect(['index', 'type'=>$model->type]);
    }

    
    /**
     * Finds the EventStatut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventStatut the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventStatut::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
