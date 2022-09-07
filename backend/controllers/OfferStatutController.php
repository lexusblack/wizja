<?php

namespace backend\controllers;

use Yii;
use common\models\OfferStatut;
use common\models\OfferStatutSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OfferStatutController implements the CRUD actions for OfferStatut model.
 */
class OfferStatutController extends Controller
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
     * Lists all OfferStatut models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = OfferStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->all();

        return $this->render('index', [
            'models' => $models
        ]);
    }

    /**
     * Displays a single OfferStatut model.
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
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = $this->findModel($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }
    /**
     * Creates a new OfferStatut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OfferStatut();
        $model->position = OfferStatut::find()->count();
        $model->is_send = 1;
        $model->is_accepted = 1;
        $model->visible_in_planning=1;
        $model->visible_in_finances = 1;
        $model->reminder_sms = 0;
        $model->reminder_mail = 0;
        $model->color = '#273a4a';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OfferStatut model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->users = explode(";",$model->reminder_users);
        $model->groups = explode(";",$model->reminder_groups);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OfferStatut model.
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
     * Finds the OfferStatut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OfferStatut the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OfferStatut::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
