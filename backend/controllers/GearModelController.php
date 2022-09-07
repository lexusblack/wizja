<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Gear;
use common\models\OuterGear;
use common\models\OuterGearModel;
use common\models\GearAttachment;
use common\models\GearModel;
use common\models\GearModelSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * GearModelController implements the CRUD actions for GearModel model.
 */
class GearModelController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class'=>AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'upload'],
                        'roles' => ['gearOurWarehouseAddFromGearBase'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['gearBaseView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['gearBaseDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['gearBaseCreate'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['gearBaseEdit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['import', 'importouter'],
                        'roles' => ['gearBaseImport'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadMainAction::className(),
                'upload'=>'/gear'

            ]
        ];
    }
    /**
     * Lists all GearModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        Url::remember();
        $searchModel = new GearModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->getRequest()->getQueryParam('t')!=null)
            $t = Yii::$app->getRequest()->getQueryParam('t');
        else
            $t='inner';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            't'=>$t
        ]);
    }

    /**
     * Displays a single GearModel model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerGearModelAttachment = new \yii\data\ArrayDataProvider([
            'allModels' => $model->gearModelAttachments,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerGearModelAttachment' => $providerGearModelAttachment,
        ]);
    }

    /**
     * Creates a new GearModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GearModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionImport($id)
    {
        $gearModel = $this->findModel($id);
        if ($gearModel)
        {
            $model = new Gear();
            $model->name = $gearModel->name;
            $model->category_id = 1;
            $model->brightness = $gearModel->brightness;
            $model->power_consumption = $gearModel->power_consumption;
            $model->width = $gearModel->width;
            $model->height = $gearModel->height;
            $model->volume = $gearModel->volume;
            $model->depth = $gearModel->depth;
            if (!$model->volume)
                $model->volume = $model->height*$model->width*$model->depth*0.000001;
            $model->weight = $gearModel->weight;
            $model->weight_case = $gearModel->weight_case;
            $model->type = $gearModel->type;
            $model->info = $gearModel->info;   
            $model->photo = $gearModel->photo;       
            if ($model->save())
            {
                $uploadDir = Yii::getAlias('@uploadroot/gear/');
                $sourceDir = Yii::getAlias('@uploadrootAll/gear/');
                copy($sourceDir.$model->photo, $uploadDir.$model->photo);
                foreach ($gearModel->gearModelAttachments as $att)
                {
                    $old_name = $att->filename;
                    $modelAtt = new GearAttachment();
                    $modelAtt->gear_id = $model->id;
                    $modelAtt->filename= $model->id."-".$att->filename;
                    $modelAtt->extension= $att->extension;
                    $modelAtt->base_name= $model->id."-".$att->base_name;
                    $modelAtt->mime_type= $att->mime_type;
                    $modelAtt->info= $att->info;
                    $uploadDir = Yii::getAlias('@uploadroot/gear-attachment/');
                    $sourceDir = Yii::getAlias('@uploadrootAll/gear-attachment/');
                    copy($sourceDir.$old_name, $uploadDir.$modelAtt->filename);
                    
                    $modelAtt->save();
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['gear/update', 'id' => $model->id]);
            }else{
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Błąd!'));
                        return $this->redirect(['gear-model/index']);
            }
        }else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'Błąd!'));
            return $this->redirect(['gear-model/index']);          
        }
        
    }

    public function actionImportouter($id)
    {
        $gearModel = $this->findModel($id);
        if ($gearModel)
        {
            $model = new OuterGearModel();
            $model->name = $gearModel->name;
                $model->category_id = 1;
            $model->power_consumption = $gearModel->power_consumption;
            $model->width = $gearModel->width;
            $model->height = $gearModel->height;
            $model->depth = $gearModel->depth;
            $model->weight = $gearModel->weight;
            $model->info = $gearModel->info;   
            $model->photo = $gearModel->photo;        
            if ($model->save())
            {
                
                $uploadDir = Yii::getAlias('@uploadroot/outer-gear/');
                $sourceDir = Yii::getAlias('@uploadrootAll/gear/');
                copy($sourceDir.$model->photo, $uploadDir.$model->photo);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['outer-gear-model/update', 'id' => $model->id]);
            }else{
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Błąd!'));
                        return $this->redirect(['gear-model/index']);
            }
        }else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'Błąd!'));
            return $this->redirect(['gear-model/index']);          
        }
        
    }
    /**
     * Updates an existing GearModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GearModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the GearModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for GearModelAttachment
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddGearModelAttachment()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('GearModelAttachment');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formGearModelAttachment', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
