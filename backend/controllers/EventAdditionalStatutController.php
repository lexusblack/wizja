<?php

namespace backend\controllers;

use Yii;
use common\models\EventAdditionalStatut;
use common\models\EventAdditionalStatutName;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventAdditionalStatutController implements the CRUD actions for EventAdditionalStatut model.
 */
class EventAdditionalStatutController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-event-additional-statut-name', 'create-statut', 'update-statut', 'delete-statut', 'order'],
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
     * Lists all EventAdditionalStatut models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EventAdditionalStatut::find()->where(['active'=>1]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventAdditionalStatut model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionOrder($id)
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = EventAdditionalStatutName::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    /**
     * Creates a new EventAdditionalStatut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventAdditionalStatut();
        $model->active = 1;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Creates a new EventAdditionalStatut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateStatut($id)
    {
        $model = new EventAdditionalStatutName();
        $model->event_additional_statut_id = $id;
        $model->active = 1;
        $model->reminder_sms = 0;
        $model->reminder_mail = 0;
        $model->reminder_pm = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('create-statut', [
                'model' => $model,
            ]);
        }
    }
        public function actionUpdateStatut($id)
    {
        $model = EventAdditionalStatutName::findOne($id);
        $model->users = explode(";",$model->reminder_users);
        $model->teams = explode(";",$model->reminder_teams);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->event_additional_statut_id]);
        } else {
            return $this->render('create-statut', [
                'model' => $model,
            ]);
        }
    }

        public function actionDeleteStatut($id)
    {
        $model = EventAdditionalStatutName::findOne($id);
        $model->active = 0;
        $model->save();

        return $this->redirect(['view', 'id' => $model->event_additional_statut_id]);
    }
    /**
     * Updates an existing EventAdditionalStatut model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->users = explode(";",$model->permission_users);
        $model->teams = explode(";",$model->permission_teams);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventAdditionalStatut model.
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
     * Finds the EventAdditionalStatut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventAdditionalStatut the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventAdditionalStatut::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for EventAdditionalStatutName
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddEventAdditionalStatutName()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('EventAdditionalStatutName');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formEventAdditionalStatutName', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
