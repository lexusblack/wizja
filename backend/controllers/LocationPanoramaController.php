<?php

namespace backend\controllers;

use Yii;
use common\models\LocationPanorama;
use common\models\Location;
use common\models\LocationPanoramaSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use common\components\filters\AccessControl;
use common\models\Attachment;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * LocationPanoramaController implements the CRUD actions for LocationPanorama model.
 */
class LocationPanoramaController extends Controller
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
                'upload'=>'/location-panorama',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all LocationPanorama models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationPanoramaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['public'=>0]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        $model->public = 1;
        echo var_dump($model);
        if ($model->save()){
            echo "ddd";
            exit;
        }
        return $this->redirect(['show', 'id' => $model->id]);
    }
    /**
     * Displays a single LocationPanorama model.
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
     * Creates a new LocationPanorama model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($locationId)
    {
        $model = new LocationPanorama();
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
     * Updates an existing LocationPanorama model.
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
     * Deletes an existing LocationPanorama model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findEditableModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionShow($id)
    {
        $model = $this->findModel($id);

        return $this->render('show', ['model'=>$model]);
    }

    
    /**
     * Finds the LocationPanorama model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationPanorama the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocationPanorama::find()->where(['id'=>$id])->andWhere(['location_id'=>Location::getIds()])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    protected function findEditableModel($id)
    {
        if (($model = LocationPanorama::find()->where(['id'=>$id])->andWhere(['location_id'=>Location::getIds(true)])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new LocationPanorama();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
            $uploadDir = Yii::getAlias('@uploadrootAll/location-panorama/');
            $sourceDir = Yii::getAlias('@uploadroot/location-panorama/');
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
