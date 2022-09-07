<?php

namespace backend\controllers;

use Yii;
use common\models\GearOuterConnected;
use common\models\GearOuterConnectedSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GearOuterConnectedController implements the CRUD actions for GearOuterConnected model.
 */
class GearOuterConnectedController extends Controller
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

    public function actionCreate($gear_id)
    {
        $model = new GearOuterConnected();
        $model->gear_id = $gear_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['gear/view', 'id' => $gear_id, '#'=>'tab_connected']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearConnected model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['gear/view', 'id' => $model->gear_id, '#'=>'tab_connected']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GearConnected model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $gear_id = $model->gear_id;
        $model->delete();

        return $this->redirect(['/gear/view', 'id'=>$gear_id, '#'=>'tab_connected']);
    }

    
    /**
     * Finds the GearOuterConnected model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearOuterConnected the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearOuterConnected::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
