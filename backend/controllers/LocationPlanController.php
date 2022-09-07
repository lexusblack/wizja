<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\LocationPlan;
use common\models\LocationPlanSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Attachment;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * LocationPlanController implements the CRUD actions for LocationPlan model.
 */
class LocationPlanController extends Controller
{

        public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    
    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/location-plan',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }


    /**
     * Displays a single LocationPlan model.
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

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $filePath = $model->getFilePath();
        return Yii::$app->response->sendFile($filePath);
    }

    /**
     * Creates a new LocationPlan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($locationId)
    {
        $model = new LocationPlan();
        $model->location_id = $locationId;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('batchCreate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LocationPlan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findEditableModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LocationPlan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findEditableModel($id)->delete();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the LocationPlan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationPlan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocationPlan::find()->where(['id'=>$id])->andWhere(['location_id'=>Location::getIds()])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    protected function findEditableModel($id)
    {
        if (($model = LocationPlan::find()->where(['id'=>$id])->andWhere(['location_id'=>Location::getIds(true)])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new LocationPlan();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
            $uploadDir = Yii::getAlias('@uploadrootAll/location-plan/');
            $sourceDir = Yii::getAlias('@uploadroot/location-plan/');
            $suorceFilename = $model->filename;
            $i=0;
            while (file_exists($uploadDir . $model->filename)) {
                $i++;
                $model->filename = $model->base_name . '-' . $i . '.' . $file->extension;             
            }
            $model->base_name = $model->base_name . '-' . $i;
            $model->save();
            copy($sourceDir.$suorceFilename, $uploadDir.$model->filename);
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }
}
