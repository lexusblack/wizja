<?php

namespace backend\controllers;

use Yii;
use common\models\RoleTranslate;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VehicleTranslateController implements the CRUD actions for VehicleTranslate model.
 */
class RoleTranslateController extends Controller
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
     * Creates a new VehicleTranslate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($role_id)
    {
        $model = new RoleTranslate();
            $model->role_id = $role_id;
        $gear = \common\models\UserEventRole::findOne($role_id);
        $model->name = $gear->name;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/user-event-role/view', 'id' => $gear->id, '#'=>'tab_translate']);
        } else {
            return $this->render('create', [
                'model' => $model, 'gear'=>$gear
            ]);
        }
    }

    /**
     * Updates an existing VehicleTranslate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/user-event-role/view', 'id' => $model->role_id, '#'=>'tab_translate']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing VehicleTranslate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['/user-event-role/view', 'id' => $model->role_id, '#'=>'tab_translate']);
    }

    
    /**
     * Finds the VehicleTranslate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleTranslate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoleTranslate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
